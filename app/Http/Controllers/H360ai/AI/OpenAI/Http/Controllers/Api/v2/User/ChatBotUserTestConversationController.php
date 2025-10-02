<?php

/**
 * @package ChatBotTestUserConversationController for User
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 04-07-2024
 */
namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\OpenAI\Entities\{
    Archive,
};
use Illuminate\Http\Response;
use Modules\OpenAI\Http\Requests\v2\ChatConversationRequest;
use Modules\OpenAI\Services\v2\ChatConversationService;
use Modules\OpenAI\Services\v2\EmbeddingService;
use Modules\OpenAI\Http\Resources\widgetChatBot\{
    BotReplyResource,
    ConversationResource,
    ChatDetailsResource
};

class ChatBotUserTestConversationController extends Controller
{
    /**
     * List chatbot conversations for a specific visitor.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $contents = (new Archive())->history(['chatbot_chat_test']);
        $contents = $contents->orderBy('created_at', 'desc')->paginate(preference('row_per_page'));
        
        return ConversationResource::collection($contents);
    }
    
    /**
     * Store a chatbot conversation.
     *
     * @param ChatConversationRequest $request
     * @return \Illuminate\Http\JsonResponse|\Modules\OpenAI\Http\Resources\widgetChatBot\BotReplyResource
     */
    public function store(ChatConversationRequest $request): \Illuminate\Http\JsonResponse|BotReplyResource
    {
        try {
            
            $chatConversationService = (new ChatConversationService);
            $chatConversationService->setChatType('chatbot_chat_test');
    
            $botOwnerId = $chatConversationService->getBotOwnerId(request('chatbot_code'));
            $checkSubscription = checkUserSubscription('word');
        
            if ($checkSubscription['status'] != 'success') {
                return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
            }

            $chatConversationService->validate($request->validated());
            return new BotReplyResource(json_decode(($chatConversationService)->askQuestion()));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
        
    /**
     * Show details of a specific chat conversation.
     *
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(int $id): AnonymousResourceCollection|JsonResponse
    {
        $contents = (new Archive())->chatDetails($id, null);

        if (count(request()->query()) > 0) {
            $contents = $contents->filter();
        }
        $contents = $contents->orderBy('created_at', 'desc')->paginate(preference('row_per_page'));

        if (!$contents->isEmpty()) {
            return ChatDetailsResource::collection($contents);
        }

        return response()->json(['error' => __(':x does not exist.', ['x' => __('Conversation')])], Response::HTTP_NOT_FOUND);
    }

    /**
     * Delete a chat conversation by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function delete(int $id): \Illuminate\Http\JsonResponse|null
    {
        try {
            (new EmbeddingService())->delete($id, 'chatbot_chat_test');
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('Conversation')])], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}


