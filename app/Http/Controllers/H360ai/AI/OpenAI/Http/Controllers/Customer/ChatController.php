<?php

/**
 * @package ChatController for Customer
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 26-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\OpenAI\Services\{
    ChatService
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
        $response = ['status' => 'error', 'message' => __('The :x does not exist.', ['x' => __('Chat Conversation')])];

        if ($this->chatService->delete($request->chatId)) {
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Chat')])];
        }

        return response()->json($response);
    }

    /**
     * update chat
     * @param Request $request
     *
     * @return [type]
     */
    public function update(Request $request)
    {
        return $this->chatService->update($request->all());
    }

    /**
     * Chat Bot With Conversation
     * 
     * @param Request $request
     *
     * @return [type]
     */
    public function chatBot(Request $request)
    {
        $data['bot'] = $this->chatService->chatBotById($request->id);
        $data['contacts'] = $this->chatService->getMyContactListWithLastMessage($data['bot']->id)->paginate(preference('row_per_page'));
        $data['assistants'] = $this->chatService->getAssistants();
        
        $data['subscriptionBots'] = $this->chatService->getAccessibleBots();
        
        $data['botPlan'] = $this->chatService->getBotPlan();
        
        $data['chatConversation'] = $this->chatService->chatConversationWithBot();
        $chat = count($data['contacts']) != 0 ? $this->chatService->chatById($data['contacts'][0]->chat_conversation_id) : [];
        
        $html = view('site.chat.message', $data)->render();

        return response()->json([
            'html' => $html,
            'chat' => $chat,
            'id' => $data['contacts'][0]->chat_conversation_id ?? []
        ]);
        
    }

    /**
     * Chat Bot With Conversation
     * 
     * @param Request $request
     *
     * @return [type]
     */
    public function conversation(Request $request)
    {
        $bot = $this->chatService->chatBotById($request->id);
        $contacts = $this->chatService->getMyContactListWithLastMessage($bot->id);
        $contacts = $contacts->paginate(preference('row_per_page'));

        return response()->json([
            'html' => $contacts,
        ]);
        
    }

}


