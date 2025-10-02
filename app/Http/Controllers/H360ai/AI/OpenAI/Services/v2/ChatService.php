<?php 

namespace Modules\OpenAI\Services\v2;

use Modules\OpenAI\Transformers\Api\v2\CharacterBotChat\BotReplyResource;
use Modules\OpenAI\Contracts\Resources\ChatContract;
use Modules\OpenAI\Entities\{Archive, ChatBot};
use Modules\OpenAI\Services\ContentService;
use Illuminate\Http\Response;
use Exception, Str, DB;

class ChatService
{
    public $production = true;
     /**
     * @var ChatContract The instance of the AI ChatContract.
     */
    public $generator;
    /**
     * Content Service
     *
     * @var object
     */
    protected $contentService;

    /**
     * ChatService constructor.
     *
     */
    public function __construct()
    {
        $this->generator = \AiProviderManager::find('openai');
        $this->contentService = new ContentService();
    }

    /**
     * Create a new chat conversation.
     *
     * @param  array  $requestData  The data for the chat conversation.
     * @return \Modules\OpenAI\Transformers\Api\v2\Chat\BotReplyResource  An array containing the bot's reply.
     * @throws \Exception
     */
    public function store(array $requestData): BotReplyResource
    {
        $subscription = null;
        $userId = $this->contentService->getCurrentMemberUserId('meta', null);

        if (! subscription('isAdminSubscribed')) {
            $userStatus = $this->contentService->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new Exception($userStatus['message'], Response::HTTP_FORBIDDEN);
            }

            $validation = subscription('isValidSubscription', $userId, 'word', null, $requestData['bot_id']);
            $subscription = subscription('getUserSubscription', $userId);
            if ($validation['status'] == 'fail' && ! auth()->user()->hasCredit('word')) {
                throw new Exception($validation['message'], Response::HTTP_FORBIDDEN);
            }
        }

        // Determine the chat bot to use based on the provided bot_id, if any
        $chatBot = isset($requestData['bot_id']) ? $this->chatBot($requestData['bot_id']) : $this->chatBot();
        // Check if a chat with the given chat_id exists
        $chat = isset($requestData['chat_id']) ? Archive::where(['id' => $requestData['chat_id'], 'type' => 'chat'])->first() : null;

        // Prepare options for generating chat content
        $options['prompt'] = filteringBadWords($requestData['prompt']);
        $options['message'] = $this->prepareMessage($chatBot, $options['prompt'], $chat);
        $options['model'] = $requestData['model'];
        $options['n'] = 1;

        DB::beginTransaction();
        try {
            // For local or staging environments, use a predefined result for testing purposes to save extra expense
            if ($this->production) {
                // Generate chat content using the AI API
                $result =  $this->generator->prepareChatOptions($options);
                $content = $this->generator->getChatContent($result);
            } else {
                $result = '{"id":"chatcmpl-8hrQqEDC5JS2Qo289Vb2vBMV5Cz3h","object":"chat.completion","created":1705464188,"model":"gpt-3.5-turbo-0613","choices":[{"index":0,"message":{"role":"assistant","content":"I am an AI-powered virtual assistant designed to assist with a wide range of tasks, including recipe recommendations and nutritional information. How can I assist you today?","functionCall":null},"finishReason":"stop"}],"usage":{"promptTokens":61,"completionTokens":31,"totalTokens":92}}
                ';
                $result = json_decode($result);
                $content = $this->generator->getChatContent($result);
            }

            if (! empty($content)) {

                if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                    $increment = subscription('usageIncrement', $subscription?->id, 'word', $content['totalWords'], $userId);
                    if ($increment  && $userId != auth()->user()->id) {
                        $this->contentService->storeTeamMeta($content['totalWords']);
                    }
                    $wordLeft = subscription('isSubscribed', auth()->id()) ? subscription('fetureUsageLeft', $subscription?->id, 'feature_word') : 0;
                }

                // Update the database based on whether the chat already exists or not
                if (! $chat) {
                    // Create a new chat record
                    $chat = new Archive();
                    $chat->user_id = auth()->id();
                    $chat->title = $requestData['prompt'];
                    $chat->unique_identifier = (string) Str::uuid();
                    $chat->provider = 'OpenAi';
                    $chat->expense = $content['totalTokens'];
                    $chat->expense_type =  'token';
                    $chat->type = 'chat';
                    $chat->total_words = $content['totalWords'];
                    $chat->save();

                    // Create user reply record
                    $userReply = new Archive();
                    $userReply->parent_id = $chat->id;
                    $userReply->user_id = auth()->id();
                    $userReply->type = 'chat_reply';
                    $userReply->user_reply = $requestData['prompt'];
                    $userReply->save();

                    // Create bot reply record
                    $botReply = new Archive();
                    $botReply->parent_id = $chat->id;
                    $botReply->raw_response = json_encode($result);
                    $botReply->provider = 'OpenAi';
                    $botReply->expense = $content['totalTokens'];
                    $botReply->expense_type = 'token';
                    $botReply->type = 'chat_reply';
                    $botReply->bot_id = $chatBot->id;
                    $botReply->bot_reply = $content['outputContents'];
                    $botReply->total_words = $content['totalWords'];
                    $botReply->prompt_tokens = $content['promptTokens'];
                    $botReply->completion_tokens = $content['completionTokens'];
                    $botReply->save();

                } else {

                    $chat->expense += $content['totalTokens'];
                    $chat->save();

                    // Update existing chat with user and bot replies
                    $userReply = new Archive();
                    $userReply->parent_id = $chat->id;
                    $userReply->user_id = auth()->id();
                    $userReply->type = 'chat_reply';
                    $userReply->user_reply = $requestData['prompt'];
                    $userReply->save();

                    // Bot Reply
                    $botReply = new Archive();
                    $botReply->parent_id = $chat->id;
                    $botReply->raw_response = json_encode($result);
                    $botReply->provider = 'OpenAi';
                    $botReply->expense = $content['totalTokens'];
                    $botReply->expense_type = 'token';
                    $botReply->type = 'chat_reply';
                    $botReply->bot_id = $chatBot->id;
                    $botReply->bot_reply = $content['outputContents'];
                    $botReply->total_words = $content['totalWords'];
                    $botReply->prompt_tokens = $content['promptTokens'];
                    $botReply->completion_tokens = $content['completionTokens'];
                    $botReply->save();
                }
                DB::commit();
    
                $newBotReply = Archive::with('metas', 'chatbot')->where('id', $botReply->id)->first();
                return new BotReplyResource($newBotReply);
                
            } else {
                throw new Exception(__("Unable to connect with the bot. Please try again."), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Retrieve a chat bot by ID or fetch the default active chat bot.
     *
     * @param  int|null  $chatBotId  The ID of the specific chat bot to retrieve.
     * @return \Modules\OpenAI\Entities\ChatBot  The retrieved chat bot or null if not found.
     */
    public function chatBot(int $chatBotId = null): ChatBot
    {
        $chatBot = ChatBot::query()->whereNull('type');
        if ($chatBotId) {
            return $chatBot->where(['id' => $chatBotId, 'status' => 'Active'])->first();
        }
        return $chatBot->where(['is_default' => 1, 'status' => 'Active'])->first();
    }


    /**
     * Prepare a message array based on the provided ChatBot, prompt, and optional chat.
     *
     * @param  \Modules\OpenAI\Entities\ChatBot  $chatBot  The ChatBot instance.
     * @param  string  $prompt  The user's prompt.
     * @param  \Modules\OpenAI\Entities\Archive|null  $chat  The optional chat instance (can be null).
     * @return array  The prepared message array.
     */
    public function prepareMessage(ChatBot $chatBot, string $prompt, ?Archive  $chat = null): array
    {
        $message = [];

        $message[] = ['role' => 'system', 'content' => $chatBot->promt];

        if ($chat) {
            $chatReply = Archive::with('metas')->where(['parent_id' => $chat->id, 'type' => 'chat_reply'])->get();
            foreach($chatReply as $reply) {
                $message[] = [
                    'role' => isset($reply->user_id) && $reply->user_id != NULL ? 'user' : 'system',
                    'content' => isset($reply->user_id) && $reply->user_id != NULL ? $reply->user_reply : $reply->bot_reply,
                ];
            }
        }

        $message[] = ['role' => 'user', 'content' => $prompt,];

        return $message;
    }

}