<?php

namespace Modules\OpenAI\Services\v2;

use Modules\OpenAI\Transformers\Api\v2\AiChat\BotReplyResource;
use Modules\OpenAI\Entities\{Archive, ChatBot};
use Modules\OpenAI\Services\ContentService;
use Illuminate\Http\Response;
use Exception, Str, DB;
use AiProviderManager;

class AiChatService
{
    private $production = true;

    private $aiProvider;

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
        $this->contentService = new ContentService();

        if (!is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'aichat');
            manageProviderValues(request('provider'), 'model', 'aichat');
        }
    }

    public function bots()
    {
        $systemBots = ChatBot::with(['metas', 'chatCategory'])->whereNull('type')->get();

        $availableBots = \Modules\OpenAI\Services\ChatService::getBotPlan();
        $userSubscribedBots = \Modules\OpenAI\Services\ChatService::getAccessibleBots();

        $bots = [];
        foreach ($systemBots as $systemBot) {
            if (!isset($availableBots[$systemBot->code]) && !in_array($systemBot->code, json_decode($userSubscribedBots) ?? [])) {
                continue;
            }
            if (!in_array($systemBot->code, json_decode($userSubscribedBots) ?? [])) {
                $systemBot->bot_plan = $availableBots[$systemBot->code];
            }
            $bots[] = $systemBot;
        }
        return $bots;
    }

    /**
     * Create a new chat conversation.
     *
     * @param  array  $requestData  The data for the chat conversation.
     * @return BotReplyResource  An array containing the bot's reply.
     * @throws \Exception
     */
    public function store(array $requestData): BotReplyResource
    {
        if (! $this->aiProvider) {
            throw new Exception(__('Provider not found.'));
        }

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
        $chatBot = isset($requestData['bot_id']) ? $this->characterChatBot($requestData['bot_id']) : $this->characterChatBot();
        // Check if a chat with the given chat_id exists
        $chat = isset($requestData['chat_id']) ? Archive::where(['id' => $requestData['chat_id'], 'type' => 'chat'])->first() : null;

        $chatReply = null;

        if ($chat) {
            $chatReply = Archive::with('metas')->where(['parent_id' => $chat->id, 'type' => 'chat_reply'])->get();
        }
        // Prepare options for generating chat content
        $requestData['chatBot'] = $chatBot;
        $requestData['chatReply'] = $chatReply;
        $requestData['prompt'] = filteringBadWords($requestData['prompt']);

        DB::beginTransaction();
        try {
            // For local or staging environments, use a predefined result for testing purposes to save extra expense
            if ($this->production) {
                // Generate chat content using the AI API
                $result =  $this->aiProvider->aiChat($requestData);
            } else {
                $result = $this->aiProvider->dummyChat();
            }

            $content = $result->content();
            $response = $result->response();
            $words = $result->words();
            $expense = $result->expense();

            if (! empty($content)) {

                if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                    $increment = subscription('usageIncrement', $subscription?->id, 'word', $words, $userId);
                    if ($increment  && $userId != auth()->user()->id) {
                        $this->contentService->storeTeamMeta($words);
                    }
                    $wordLeft = subscription('isSubscribed', auth()->id()) ? subscription('fetureUsageLeft', $subscription?->id, 'feature_word') : 0;
                }

                // Update the database based on whether the chat already exists or not
                if (! $chat) {
                    // Create a new chat record
                    $archive = ArchiveService::create([
                        'user_id' => auth()->id(),
                        'title' => $requestData['prompt'],
                        'unique_identifier' => (string) Str::uuid(),
                        'provider' => $requestData['provider'],
                        'expense' => $expense,
                        'expense_type' => 'token',
                        'type' => 'chat',
                        'total_words' => $words,
                    ]);
                   

                    // Create user reply record
                    ArchiveService::create([
                        'parent_id' => $archive->id,
                        'user_id' => auth()->id(),
                        'type' => 'chat_reply',
                        'user_reply' => $requestData['prompt'],
                    ]);

                    // Create bot reply record
                    $botReply = ArchiveService::create([
                        'parent_id' => $archive->id,
                        'raw_response' => json_encode($response),
                        'provider' => $requestData['provider'],
                        'expense' => $expense,
                        'expense_type' => 'token',
                        'type' => 'chat_reply',
                        'bot_id' => $chatBot->id,
                        'bot_reply' => $content,
                        'total_words' => $words,
                    ]);

                } else {

                    $chat->expense += $expense;
                    $chat->save();

                    // Update existing chat with user and bot replies
                    ArchiveService::create([
                        'parent_id' => $chat->id,
                        'user_id' => auth()->id(),
                        'type' => 'chat_reply',
                        'user_reply' => $requestData['prompt'],
                    ]);

                    // Bot Reply
                    $botReply = ArchiveService::create([
                        'parent_id' => $chat->id,
                        'raw_response' => json_encode($result),
                        'provider' => $requestData['provider'],
                        'expense' => $expense,
                        'expense_type' => 'token',
                        'type' => 'chat_reply',
                        'bot_id' => $chatBot->id,
                        'bot_reply' => $content,
                        'total_words' => $words,
                    ]);
                }
                DB::commit();
    
                $newBotReply = Archive::with('metas', 'chatbot')->where(['id'=> $botReply->id, 'type' => 'chat_reply'])->first();
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
    public function characterChatBot(int $chatBotId = null): ChatBot
    {
        $chatBot = ChatBot::query();
        if ($chatBotId) {
            return $chatBot->where(['id' => $chatBotId, 'status' => 'Active'])->first();
        }
        return $chatBot->where(['is_default' => 1, 'status' => 'Active'])->first();
    }
}
