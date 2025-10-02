<?php

/**
 * @package ChatBotController for Customer
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * 
 * @created 19-02-2023
 */

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\{
    ChatBotWidgetResource,
    ChatBotImageResource
};
use Modules\OpenAI\Http\Requests\v2\{
    ChatBotWidgetRequest,
    ChatBotWidgetUpdateRequest
};
use Modules\OpenAI\Services\v2\ChatBotWidgetService;
use Modules\OpenAI\Services\v2\TeamMemberService;
use Modules\OpenAI\Entities\ChatBot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\{
    Response,
    JsonResponse
};
use Exception;

class ChatBotWidgetController extends Controller
{
    /**
     * @var ChatBotWidgetService The instance of the chat service.
     */
    protected $chatBotWidgetService;

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the AI provider and chat service.
     */
    public function __construct()
    {
        $this->chatBotWidgetService = new ChatBotWidgetService();
    }

    /**
     * Display a paginated list of ChatBot resources ordered by creation date.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $chatBot = ChatBot::with(['user', 'metas'])->where('user_id', auth()->user()->id)->whereType('widgetChatBot')->filter();
        return ChatBotWidgetResource::collection($chatBot->paginate(preference('row_per_page')));
    }

    /**
     * Store a new ChatBot resource.
     *
     * @param  ChatBotWidgetRequest $request
     * @return JsonResponse
     */
    public function store(ChatBotWidgetRequest $request): JsonResponse
    {
        try {
            $chat = $this->chatBotWidgetService->store($request->except('_token'));
            return response()->json(['data' => $chat], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            
        }
    }

    /**
     * Update a ChatBot resource based on the provided request data.
     *
     * @param string $code The unique code of the ChatBot to update.
     * @param ChatBotWidgetUpdateRequest $request The HTTP request containing the data for updating the ChatBot.
     *
     * @return JsonResponse The JSON response containing the updated ChatBot data or an error message.
     */
    public function update(string $code, ChatBotWidgetUpdateRequest $request): JsonResponse
    {
        try {
            $chatBot = $this->chatBotWidgetService->update($code, $request->except('_token'));
            return response()->json(['data' => $chatBot], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified ChatBot resource.
     *
     * @param string $code
     * @return JsonResponse|ChatBotWidgetResource
     */
    public function show(string $code): JsonResponse|ChatBotWidgetResource
    {
        $chatBot = ChatBot::where(['type' => 'widgetChatBot', 'code' => $code, 'user_id' => auth()->user()->id])->first();
        if ($chatBot) {
            return new ChatBotWidgetResource($chatBot);
        }
        return response()->json(['error' =>  __(':x does not exist.', ['x' => __('Chatbot')])], Response::HTTP_NOT_FOUND);
    }

    /**
     * Delete a ChatBot Widget by its unique code.
     *
     * @param string $code The unique code of the ChatBot Widget to be deleted.
     * @return JsonResponse The JSON response indicating the success or failure of the deletion operation.
     */
    public function delete(string $code): JsonResponse
    {
        try {
            $this->chatBotWidgetService->delete($code);
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('Chatbot')])], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a ChatBot Widget's Image by its unique code.
     *
     * @param string $code The unique code of the ChatBot Widget to be deleted.
     * @param Request $request The HTTP request containing the data for updating the ChatBot.
     * @return JsonResponse The JSON response indicating the success or failure of the deletion operation.
     */
    public function destroyImage(string $code, Request $request): JsonResponse
    {
        try {
            $chatbot = $this->chatBotWidgetService->deleteImage($code, $request->except('_token'));
            return response()->json(['data' => new ChatBotImageResource($chatbot)], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Method to retrieve dashboard materials and return a JSON response.
     *
     * @return JsonResponse The JSON response containing the dashboard materials data or an error message.
     */
    public function dashboard(): JsonResponse
    {
        try {
            $dashboard = $this->chatBotWidgetService->dashboardData();
            return response()->json(['data' => $dashboard], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified ChatBot resource.
     *
     * @param  string $code
     * @return JsonResponse|ChatBotWidgetResource
     */
    public function details(string $code): JsonResponse|ChatBotWidgetResource
    {
        $chatBot = ChatBot::where(['type' => 'widgetChatBot', 'code' => $code])->first();

        // Check if the user has access
        if (!(new TeamMemberService())->hasTeamMemberAccess($chatBot->user_id, 'chatbot')) {
            return response()->json(['error' => __('You do not have permission to access this feature')], Response::HTTP_FORBIDDEN);
        }

        if ($chatBot) {
            return new ChatBotWidgetResource($chatBot);
        }
        return response()->json(['error' =>  __(':x does not exist.', ['x' => __('Chatbot')])], Response::HTTP_NOT_FOUND);
    }
}
