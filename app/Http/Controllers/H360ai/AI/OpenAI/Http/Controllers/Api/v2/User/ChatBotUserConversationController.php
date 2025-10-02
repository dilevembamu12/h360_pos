<?php

/**
 * @package ChatBotUserConversationController for User
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 17-06-2024
 */
namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\OpenAI\Entities\{
    Archive
};
use Illuminate\Http\Response;
use Modules\OpenAI\Http\Resources\widgetChatBot\{
    ConversationResource,
    ChatDetailsResource
};
use Modules\OpenAI\Services\v2\ChatConversationService;

class ChatBotUserConversationController extends Controller
{
    /**
     * List chatbot conversations for a specific visitor.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $contents = (new Archive())->history(['chatbot_chat'])->filter();
        $contents = $contents->paginate(preference('row_per_page'));
        
        return ConversationResource::collection($contents);
    }
        
    /**
     * Show details of a specific chat conversation.
     *
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(int $id): AnonymousResourceCollection|JsonResponse
    {
        $contents = (new Archive())->chatDetails($id);

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
            (new ChatConversationService)->delete($id, 'chatbot_chat');
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('Conversation')])], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Generate a CSV of chatbot chat history.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function csv()
    {
        $contents = (new Archive())->history(['chatbot_chat'])->filter()->get();
        return ConversationResource::collection($contents);
    }
}


