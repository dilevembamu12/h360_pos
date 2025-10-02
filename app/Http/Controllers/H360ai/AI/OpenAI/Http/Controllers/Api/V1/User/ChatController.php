<?php

/**
 * @package ChatController for Admin
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 26-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\OpenAI\Services\{
    ChatService
};
use Modules\OpenAI\Entities\{
    ChatConversation,
    ChatBot
};

use Modules\OpenAI\Http\Resources\{
    ChatConversationResource,
    ChatAssistantResource
};

class ChatController extends Controller
{
    /**
     * Chat Service
     *
     * @var object
     */
    protected $chatService;

    /**
     * Constructor
     * 
     * @param ChatService $chatService
     */
    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * All chat Assistants
     *
     * @return [type]
     */
    public function allChatAssistants()
    {
        $chatAssistants = $this->chatService->getAllAssistants();

        app()->instance('subscriptionBots', $this->chatService->getAccessibleBots());
        app()->instance('botPlan', $this->chatService->getBotPlan());

        $responseData = ChatAssistantResource::collection($chatAssistants)->response()->getData(true);
        return $this->response($responseData);
    }

    /**
     * Store Chat via service
     * @param Request $request
     *
     * @return [type]
     */
    public function saveChat($chat)
    {
        return $this->chatService->save($chat);
    }

    /**
     * View Chat history
     * @param mixed $id
     *
     * @return [type]
     */
    public function history($id)
    {
       return $this->chatService->chatById($id);
    }
    
    /**
     * Delete chat
     * @param Request $request
     *
     * @return [type]
     */
    public function delete(Request $request)
    {   
        $configs = $this->initialize([], $request->all());
       
        if ( $this->chatService->delete($request->chatId) ) {

            $chatConversation = ChatConversation::with(['user', 'user.metas'])->where('user_id', auth('api')->user()->id)->orderBy('id', 'DESC');
            $data = [
                'data' => ChatConversationResource::collection($chatConversation->paginate($configs['rows_per_page'])),
                'pagination' => $this->toArray($chatConversation->paginate($configs['rows_per_page'])->appends($request->all()))
            ];

            return $this->okResponse($data, __('The :x has been successfully deleted.', ['x' => __('Chat')]));
        }


        return $this->notFoundResponse([], __('The :x does not exist.', ['x' => __('Chat Conversation')]));
       
    }

    /**
     * update chat
     * @param Request $request
     *
     * @return [type]
     */
    public function update(Request $request)
    {
        $configs = $this->initialize([], $request->all());

        if ( $this->chatService->update($request->all()) ) {

            $chatConversation = ChatConversation::with(['user', 'user.metas'])->where('user_id', auth('api')->user()->id)->orderBy('id', 'DESC');
            $data = [
                'data' => ChatConversationResource::collection($chatConversation->paginate($configs['rows_per_page'])),
                'pagination' => $this->toArray($chatConversation->paginate($configs['rows_per_page'])->appends($request->all()))
            ];

            return $this->okResponse( $data, __('The :x has been successfully updated.', ['x' => __('Chat')]));
        }

        return $this->notFoundResponse([], __('The :x does not exist.', ['x' => __('Chat Conversation')]));
    }
}


