<?php

/**
 * @package ChatBotCOnversationController for User
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 17-06-2024
 */
namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Entities\{
    Archive,
};
use Illuminate\Http\Response;
use Modules\OpenAI\Http\Requests\v2\ChatConversationRequest;
use Modules\OpenAI\Services\v2\ChatConversationService;
use Modules\OpenAI\Http\Resources\widgetChatBot\{
    BotReplyResource,
    ConversationResource,
    ChatDetailsResource
};
use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Services\v2\TeamMemberService;

class ChatBotConversationController extends Controller
{
    /**
     * List chatbot conversations for a specific visitor.
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($id): AnonymousResourceCollection
    {

        $contents = (new Archive())->chatBotConversations($id, 'visitor_id', 'chatbot_chat');
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
        dd(111);
        try {
            $chatConversationService = (new ChatConversationService);
            
            if (is_null($request->visitor_id)) {
                $id = $chatConversationService->createVisitorId();
                request()->merge(['visitor_id' => $id]);
            }

            if ( !empty(request('chatbot_chat_id')) && !$chatConversationService->checkConversationExists(request('chatbot_chat_id'))) {
                return response()->json(['error' => __('Please create a new conversation to proceed.')], Response::HTTP_NOT_FOUND);
            }

            $botOwnerId = $chatConversationService->getBotOwnerId(request('chatbot_code'));

            // Check if the user has access
            if (!(new TeamMemberService())->hasTeamMemberAccess($botOwnerId->user_id, 'chatbot')) {
                return response()->json(['error' => __('You do not have permission to access this feature')], Response::HTTP_FORBIDDEN);
            }

            $userId = (new ContentService())->getCurrentMemberUserId('meta', null, ['user_id' => $botOwnerId->user_id]);

            $user = \app\Models\User::where('id', $userId)->first();

            if (!subscription('isAdminSubscribed', $userId)) {
                $contentService = new ContentService();
                $userStatus = $contentService->checkUserStatus($userId, 'meta');
                
                if ($userStatus['status'] == 'fail') {
                    return response()->json(['error' => __('Something went wrong. Try to contact with the administration.')], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            
                $validation = subscription('isValidSubscription', $userId, 'word');
                if ($validation['status'] == 'fail' && !$user->hasCredit('word')) {
                    return response()->json(['error' => __('Something went wrong. Try to contact with the administration.')], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
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
    public function show($visitorId, $id): AnonymousResourceCollection|JsonResponse|Array
    {
        $contents = (new Archive())->chatDetails($id, $visitorId);

        if (count(request()->query()) > 0) {
            $contents = $contents->filter();
        }
        $contents = $contents->orderBy('id', 'desc')->paginate(preference('row_per_page'));

        if (!$contents->isEmpty()) {
            return ChatDetailsResource::collection($contents)->response()->getData(true);
        }
        
        return response()->json(['error' => __(':x does not exist.', ['x' => __('Conversation')])], Response::HTTP_NOT_FOUND);
    }

    /**
     * Delete a chat conversation by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function destroy($visitorId, $id): JsonResponse|null
    {
        (new ChatConversationService)->delete($id, 'chatbot_chat', $visitorId);
        return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('Conversation')])], Response::HTTP_OK);
    }
}


