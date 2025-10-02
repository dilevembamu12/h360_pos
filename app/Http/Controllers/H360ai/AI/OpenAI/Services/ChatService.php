<?php

/**
 * @package ChatService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 30-05-2023
 */

 namespace Modules\OpenAI\Services;


use Illuminate\Support\Facades\DB;
use Modules\OpenAI\Entities\{
    Archive,
    Chat,
    ChatBot,
    ChatConversation,
};

use App\Models\{
    Team,
    TeamMemberMeta
};
use App\Lib\Env;

use Illuminate\Http\{
    JsonResponse,
    Response
};

use Exception;
use Modules\OpenAI\Entities\OpenAI;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Modules\Subscription\Entities\Package;
use Infoamin\Installer\Helpers\PermissionsChecker;

 class ChatService
 {
    use ApiResponse;
    /**
     * @var [type]
     */
    protected $formData;
    protected $promt;

    /**
     * Ai key
     *
     * @var string
     */
    protected $aiKey = false;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($formData = null, $promt = null)
    {
        $this->formData = $formData;
        $this->promt = $promt;
    }

    /**
     * URL of API
     *
     * @return string
     */
    public function getUrl()
    {
        return config('openAI.chatUrl');
    }

    /**
     * URL of API
     *
     * @return string
     */
    public function getModel()
    {
        return config('openAI.chatModel');
    }

    /**
     * Token of chat module
     *
     * @return string
     */
    public function getToken()
    {
        return preference('max_token_length');
    }

    /**
     * get Api Key
     *
     * @param string|null $keyName
     * @return string
     */
    public function aiKey($keyName = 'openai'): string
    {
        if (!config('aiKeys.' . strtoupper($keyName) . '.API_KEY')) {
            $this->envKey($keyName);
            return $this->aiKey;
        } else {
            return config('aiKeys.' . strtoupper($keyName) . '.API_KEY');
        }
    }

    /**
     * Set Ai key from preference to.env file
     *
     * @param string $keyName
     * @return void
     */
    public function envKey($keyName): void
    { 
        if (!is_null(preference($keyName))) {
            $preferenceAiKey = preference($keyName);
        } else {
            $preferenceAiKey = preference($keyName.'_api');
        }
        $checker = new PermissionsChecker;
        $permissions = $checker->checkPermission([".env" => 666]);
        if ($permissions['permissions'][0]['isActive'] == true) {
            Env::set(strtoupper($keyName), $preferenceAiKey ? $preferenceAiKey : false);
            $this->aiKey = $preferenceAiKey ?? false;
        }else {
            $this->aiKey = false;
        }
    }

    /**
     * Client
     *
     * @return \OpenAI\Client
     */
    public function client()
    {
        return \OpenAI::client($this->aiKey());
    }

    /**
     * Image create
     *
     * @param mixed $data
     * @return array
     */
    public function createChat($data)
    {
        $this->formData = $data;
        $this->formData['promt'] = filteringBadWords($this->formData['promt']);
        return $this->preparePromt();
    }

    /**
     * Assistant
     *
     * @param string|null $chatBotId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function assistant($chatBotId = null)
    {
        $chatBot = ChatBot::query();
        if ($chatBotId) {
            return $chatBot->where(['id' => $chatBotId])->first();
        }
        return $chatBot->where('is_default', 1)->whereNull("type")->first();
    }

    /**
     * Chat Conversation
     * 
     * @param string|null $chatBotId
     * @param string|null $conversationId
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function conversation($botId, $conversationId)
    {
        $conversation = ChatConversation::where('id', $conversationId)->where('user_id', auth()->user()->id)->where('bot_id', $botId)->exists();
        return $conversation ? Chat::where('chat_conversation_id', $conversationId)->whereNotNull('user_id')->orderByDesc('created_at')->take(6)->get()->reverse() : [];
    }

    /**
     * Prepare data
     * @param \Illuminate\Database\Eloquent\Model $chats
     * @return array
     */
    public function prepareData($chats)
    {

        $data = [];

        $data[] = [
            "role" => "system",
            "content" => $this->assistant($this->formData['botId'])->promt,
        ];

        if (!empty($chats)) {
            foreach($chats as $chat) {
                $data[] = [
                    "role" => ($chat->bot_id == NULL && $chat->bot_message == NULL) ? 'user' : "system",
                    "content" => ($chat->bot_id == NULL && $chat->bot_message == NULL) ? $chat->user_message : $chat->bot_message,
                ];
            }
        }

        $data[] = [
            "role" => "user",
            "content" => $this->formData['promt'],
        ];

        return $data;
    }


    /**
     * Prepare promt
     *
     * @return array
     */
    public function preparePromt()
    {
        $chats = ( isset($this->formData['chatId']) && !empty($this->formData['chatId']) ) ? $this->conversation($this->formData['botId'], $this->formData['chatId']): [];
        $model = request('model') ?? $this->getModel();
        $this->promt = ([
            'model' => $model,
            'messages' => $this->prepareData($chats),
            'temperature' => 1,
            'max_tokens' => (int) $this->getToken(),
            "top_p" => 1,
            "n" => 1,
            "stream" => OpenAI::requiredStremedData() ? true : false,
            "presence_penalty" => 0,
            "frequency_penalty" => 0
        ]);

        return $this->getResponse();
    }

    /**
     * Get Response
     *
     * @return array
     */
    private function getResponse()
    {
        return OpenAI::requiredStremedData() ? 
        $this->client()->chat()->createStreamed($this->promt) :
        $this->client()->chat()->create($this->promt)->toArray();
    }

    /**
     * Curl Request
     *
     * @return string
     */
    public function makeCurlRequest()
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->getUrl(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => config('openAI.ssl_verify_host'),
        CURLOPT_SSL_VERIFYPEER => config('openAI.ssl_verify_peer'),
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($this->promt),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->aiKey(),
        ),
        ));

        // Make API request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        // Close cURL session
        curl_close($curl);
        $response = !empty($response) ? $response : $err;
        $response =  json_decode($response, true);
        return $response;
    }


    /**
     * Store Images
     * @param mixed $data
     *
     * @return array
     */
    public function save($chatInfo)
    {
        try {
            if (!OpenAI::requiredStremedData()) {
                $token = $chatInfo['usage']['total_tokens'];
                $words = preference('word_count_method') == 'token' ? subscription('tokenToWord', $chatInfo['usage']['total_tokens']) : countWords($chatInfo['choices'][0]['message']['content']);
                $characters = strlen($chatInfo['choices'][0]['message']['content']);
                $message = $chatInfo['choices'][0]['message']['content'];
                } else {
                    $token = subscription('tokenToWord', count(explode(' ', ($chatInfo))));
                    $wordCount = count(explode(' ', ($chatInfo)));
                    $words = preference('word_count_method') == 'token' ? subscription('tokenToWord', $wordCount) : countWords($chatInfo);
                    $characters = strlen($chatInfo);
                    $message = $chatInfo;
                }

            DB::beginTransaction();
            if (!empty(request('chatId'))) {
                $conversationId = request('chatId');
                !empty(request('botId')) ? ChatConversation::where('id', $conversationId)->update(['bot_id' => request('botId')]) :'';
            } else {
                $newConversation = new ChatConversation();
                $newConversation->title = request('promt');
                $newConversation->user_id = auth('api')->user()->id;
                $newConversation->bot_id = request('botId');
                $newConversation->save();
                $conversationId = $newConversation->id;
            }
            // User Message
            $chat = new Chat();
            $chat->chat_conversation_id = $conversationId;
            $chat->user_id = auth('api')->user()->id;
            $chat->user_message = request('promt');
            $chat->tokens = $token;
            $chat->words = $words;
            $chat->characters = $characters;
            $chat->save();

            // Bot Reply
            $botChat = new Chat();
            $botChat->chat_conversation_id = $conversationId;
            $botChat->bot_id = request('botId');
            $botChat->bot_message = $message;
            $botChat->save();
            DB::commit();

            $botImage = $this->assistant(request('botId'))->fileUrl();
            $totalConversation = ChatConversation::with('chats')->where('user_id', auth('api')->user()->id)->where('bot_id', request('botId'))->count();

        } catch(Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return ['apiResponse' => $chatInfo, 'newChatId' => request('chatId'), 'id' => $conversationId, 'botImage' => $botImage, 'totalConversation' => $totalConversation];
    }

    /**
     * Last chat message
     * @return [type]
     */
    public static function getMyContactListWithLastMessage($id)
    {
        $chat = Chat::leftJoin('chat_conversations as conversation', 'chats.chat_conversation_id', '=', 'conversation.id')
                ->select('chats.id', 'chats.chat_conversation_id', 'conversation.title', 'chats.created_at')
                ->where('chats.user_id', auth()->user()->id)
                ->where('conversation.user_id', auth()->user()->id)
                ->where('conversation.bot_id', $id)
                ->orderBy('created_at', 'desc');

        $chat = $chat->joinSub(function ($query) {
                $query->select('chat_conversation_id', \DB::raw('MAX(created_at) as max_created_at'))
                    ->from('chats')
                    ->groupBy('chat_conversation_id');
                }, 'latest_messages', function ($join) {
                    $join->on('chats.chat_conversation_id', '=', 'latest_messages.chat_conversation_id')
                    ->on('chats.created_at', '=', 'latest_messages.max_created_at');
                })
                ->groupBy('chats.chat_conversation_id');
                
        return $chat;
    }

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Chat::query();
    }

    /**
     * Find chat by id
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function chatById($id)
    {
        $chatExists =  self::model()->whereChatConversationId($id)->where('user_id', auth()->user()->id)->exists();
        return $chatExists ? self::model()->whereChatConversationId($id)->get() : [];
    }


    /**
     * Bot Name
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function getBotName()
    {
        return ChatBot::select('id', 'name', 'code', 'message')->whereNull("type")->where('is_default', 1)->first();
    }

    /**
     * Delete chat
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $data = ChatConversation::find($id);
        return !empty($data) ? $data->delete() : false;
    }

    /**
     * Update chat title
     *
     * @param array $data
     * @return bool
     */
    public function update($data)
    {
        if ($chat = ChatConversation::where('id', $data['chatId'])->first()) {
            $chat->title = $data['name'];
            return $chat->save();
        }
        return false;
    }

    /**

     * Team member meta insert or update
     * @param array $words
     *
     * @return bool|array
     */
    public function storeTeamMeta($words)
    {
        $memberData = Team::getMember(auth()->user()->id);
        if (!empty($memberData)) {
            $usage = TeamMemberMeta::getMemberMeta($memberData->id, 'word_used');
            if (!empty($usage)) {
                return $usage && $usage->increment('value', $words); 
            }
        }
        return false;
    }

    /**
     * Find chat bot by id
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function chatBotById($id)
    {
        return $this->assistant($id);
    }

    /**
     * Get all assistant based on last chat
     * @return [type]
     */
    public static function getAssistants()
    {

        $chat = ChatBot::leftJoin('chats', 'chat_bots.id', '=', 'chats.bot_id')
                ->select('chat_bots.id', 'chat_bots.name', 'chat_bots.role', 'chat_bots.code')
                ->where('chat_bots.status', 'Active')
                ->whereNull('chat_bots.type')
                ->orderBy('chats.created_at', 'desc');

        $chat = $chat->joinSub(function ($query) {
                $query->select('bot_id', \DB::raw('MAX(created_at) as max_created_at'))
                    ->from('chats')
                    ->groupBy('bot_id');
                }, 'latest_messages', function ($join) {
                    $join->on('chats.bot_id', '=', 'latest_messages.bot_id')
                    ->on('chats.created_at', '=', 'latest_messages.max_created_at');
                })
                ->groupBy('chats.bot_id');
        
        $chats = $chat->get();
        $ids =   $chat->pluck('chat_bots.id')->toArray();

        $chatBots = ChatBot::select('id', 'name', 'role', 'code')->whereNull('type')->where('status', 'Active')->whereNotIn('id', $ids)->get();

        return ( count($chats) != 0 ) ? $chats->concat($chatBots) : $chatBots;
    }
   
    /**
     * Chat Conversation With Bot
     * 
     * @return array
     */
    public static function chatConversationWithBot($botId = null)
    {
        if (!auth()->user()) {
            return [];
        }

        if ($botId != Null) {
            return  ChatConversation::select(DB::raw('COUNT(*) as count'))
                ->where('user_id', auth()->user()->id)
                ->where('bot_id', $botId)
                ->value('count');
        }
        
        return ChatConversation::select('bot_id', DB::raw('COUNT(*) as count'))
            ->where('user_id', auth()->user()->id)
            ->groupBy('bot_id')
            ->pluck('count', 'bot_id')
            ->toArray();
    }


    /**
     * Get Bot information Based on Last Chat
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function getChatBot()
    {
        $chat = ChatBot::leftJoin('chat_conversations', 'chat_bots.id', '=', 'chat_conversations.bot_id')
                ->leftJoin('chats', 'chat_bots.id', '=', 'chats.bot_id')
                ->select('chat_bots.id', 'chat_bots.name', 'chat_bots.role', 'chat_bots.code', 'chat_bots.message')
                ->where('chat_bots.status', 'Active')
                ->where('chat_conversations.user_id', auth()->user()->id)
                ->orderBy('chats.created_at', 'desc');

        $chat = $chat->joinSub(function ($query) {
                $query->select('bot_id', \DB::raw('MAX(created_at) as max_created_at'))
                    ->from('chats')
                    ->groupBy('bot_id');
                }, 'latest_messages', function ($join) {
                    $join->on('chats.bot_id', '=', 'latest_messages.bot_id')
                    ->on('chats.created_at', '=', 'latest_messages.max_created_at');
                })
                ->groupBy('chats.bot_id');
        
        $chatBot = $chat->first();
        
        if ($chatBot && ChatBot::where('code', $chatBot->code)->exists()) {
            return $chatBot;
        }
        
        return empty($chatBot) ? self::getBotName() : $chatBot;
    }
    
    /**
     * Get user accessible bot list
     */
    public static function getAccessibleBots()
    {
        if (empty(auth()->user()) || (!auth()->user()->hasCredit('word') && subscription('isValidSubscription', auth()->user()->id, 'word')['status'] != 'success') && !subscription('isAdminSubscribed')) {
            return json_encode([]);
        }
        
        $allAssistants = self::getAssistants()->pluck('code');
        
        if (subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
            return $allAssistants;
        }
        
        if (preference('credit_balance_priority', 'subscription') == 'onetime') {
            if (auth()->user()->hasCredit('word')) {
                return $allAssistants;
            } else {
                return subscription('getUserSubscription', auth()->user()->id)?->chatAssistants;
            }
        }
        
        if (subscription('isValidSubscription', auth()->user()->id, 'word')['status'] == 'success') {
            return subscription('getUserSubscription', auth()->user()->id)?->chatAssistants;
        }
        
        return $allAssistants;
    }
    
    /**
     * Get Bot Plan
     */
    public static function getBotPlan()
    {
        $packages = Package::all();
        
        $bots = [];
        foreach ($packages as $key => $package) {
            $bots += array_fill_keys(array_values(json_decode($package->chatAssistants)), $package->name);
        }
        
        return $bots;
    }

    /**
     * Get all assistant based on last chat
     * 
     * @return [type]
     */
    public static function getAllAssistants()
    {
        $chat = ChatBot::leftJoin('chats', 'chat_bots.id', '=', 'chats.bot_id')
                ->select('chat_bots.*')
                ->where('chat_bots.status', 'Active')
                ->orderBy('chats.created_at', 'desc');

        $chat = $chat->joinSub(function ($query) {
                $query->select('bot_id', \DB::raw('MAX(created_at) as max_created_at'))
                    ->from('chats')
                    ->groupBy('bot_id');
                }, 'latest_messages', function ($join) {
                    $join->on('chats.bot_id', '=', 'latest_messages.bot_id')
                    ->on('chats.created_at', '=', 'latest_messages.max_created_at');
                })
                ->groupBy('chats.bot_id');
        
        $chats = $chat->get();
        $ids =   $chat->pluck('chat_bots.id')->toArray();

        $chatBots = ChatBot::select('*')->where('status', 'Active')->whereNotIn('id', $ids)->whereNull('type')->get();
        
        return ( count($chats) != 0 ) ? $chats->concat($chatBots) : $chatBots;
    }

    /**
     * @param mixed $data
     * @param mixed $subscription
     * @param mixed $chatController
     * @param mixed $chatService
     * @param mixed $userId
     * 
     * @return [type]
     */
    public function streamResponse($data, $subscription, $chatController, $chatService, $userId)
    {
        return response()->stream(function () use ($data, $subscription, $chatController, $chatService, $userId) { 
            try {
               $result = $this->createChat($data);
               $textValue = $chat = '';
               ob_start();
               foreach ($result as $response) {
                    if ( isset($response["choices"][0]["delta"]["content"])) {
                        $textValue = $response["choices"][0]["delta"]["content"];
                        $chat .= $response["choices"][0]["delta"]["content"];
                    }

                    if (connection_aborted()) {
                        break;
                    }
                    echo $textValue;
                    ob_flush();
                    flush();
                }
                if (!empty($chat)) {
                        $words = count(explode(' ', ($chat)));
                        $response = $chatController->saveChat($chat);
                        $response['usage']['words'] =  $words;
                        $response['balanceReduce'] = 'onetime';
                        if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                            $increment = subscription('usageIncrement', $subscription?->id, 'word', $words, $userId);
                            $response['balanceReduce'] = app('user_balance_reduce');
                            if ($increment  && $userId != auth()->user()->id) {
                                $chatService->storeTeamMeta($words);
                            }
                        }
                    
                    echo ' __chatId:' . $response['id'] ?? '0';
                    return $this->successResponse($response);
                }
                    return $this->unprocessableResponse([
                        'response' => $chat['error']['message'],
                        'status' => 'failed',
                    ]);
               
            } catch (Exception $e) {
                $response = $e->getMessage();
                $data = [
                    'text' => $response,
                    'status' => false,
                    'code' => 500,
                ];
                echo json_encode($data);
                ob_flush();
                flush();
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'json',
        ]); 
    }

    /**
     * @param mixed $data
     * @param mixed $subscription
     * @param mixed $chatController
     * @param mixed $chatService
     * @param mixed $userId
     * 
     * @return [type]
     */
    public function generalresponse($data, $subscription, $chatController, $chatService, $userId)
    {
         try {
            $chat = $chatService->createChat($data);
            if (empty($chat['error'])) {
                $words = subscription('tokenToWord', $chat['usage']['total_tokens']);
                $response = $chatController->saveChat($chat);
                $response['usage']['words'] = $words;
                $response['balanceReduce'] = 'onetime';
                if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                    $increment = subscription('usageIncrement', $subscription?->id, 'word', $words, $userId);
                    $response['balanceReduce'] = app('user_balance_reduce');
                    if ($increment  && $userId != auth()->user()->id) {
                        $chatService->storeTeamMeta($words);
                    }
                }
                return $this->successResponse($response);
            }
            return $this->unprocessableResponse([
                'response' => $chat['error']['message'],
                'status' => 'failed',
            ]);
        } catch(Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Get conversation data, combining chat history and image details.
     *
     * @param  ImageService $imageService  The ImageService instance for image data retrieval.
     * @return array Combined array of conversation history and image information.
     */
    public function conversationData($imageService): array
    {
        $history = (new Archive())->history()->get()->toArray();
        $image = $imageService->model()->whereNull('parent_id');

        $userRole = auth()->user()->roles()->first();

        if ($userRole->type == 'user') {
            $image = $image->where('user_id', auth()->user()->id);
        }
        
        $data['image'] = $image->groupBy('meta->created-at')->get();

        foreach ($data['image'] as &$subArray) {
            if (filled($data['image'][0])) {
                $subArray['type'] = class_basename($data['image'][0]);
            }
        }
        $image = $data['image']->toArray();
   
        $mergedArray = array_merge($image, $history);
        usort($mergedArray, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        foreach ($mergedArray as $key => $value) {
            if (isset($value['created_at']) && Carbon::hasFormat($value['created_at'], 'Y-m-d\TH:i:s.u\Z')) {
                $mergedArray[$key]['created_at'] = timeToGo($value['created_at'], false, 'ago');
            }
        }

        return $mergedArray;
    }

    /**
     * Destroy a chat and its replies.
     *
     * @param  int  $chatId  [The ID of the chat to be destroyed.]
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $chatId, string $type, $data = []): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Find the chat or throw an exception if not found            
            $chat = Archive::where(['id' => $chatId, 'type' => $type])->first() ?? throw new Exception(__(':x does not exist.', ['x' => __($type)]), Response::HTTP_NOT_FOUND);
            $chat->save();
            $chat->delete();

            $chatReplies = Archive::with('metas')->where(['parent_id' => $chatId])->get();
            if (! $chatReplies->isEmpty()) {
                foreach ($chatReplies as $reply) {
                    $reply->unsetMeta($data);
                    $reply->save();
                    $reply->delete();
                }
            }
            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
