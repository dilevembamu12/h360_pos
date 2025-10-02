<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\{
    Response,
    JsonResponse
};
use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatBotTrainingResource;
use Modules\OpenAI\Services\v2\ChatBotTrainingService;
use Modules\OpenAI\Http\Requests\v2\{
    ChatBotWidgetTrainingRequest,
    ChatBotWidgetMaterialRequest,
    ChatBotFetchRequest
};
use Illuminate\Http\Request;
use Modules\OpenAI\Entities\{
    EmbededResource,
    ChatBot
};

class ChatBotTrainingController extends Controller
{

    /**
     * @var ChatBotTrainingService The instance of the chat service.
     */
    protected $chatBotTrainingService;

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the AI provider and chat service.
     */
    public function __construct()
    {
        $this->chatBotTrainingService = new ChatBotTrainingService();
    }

    /**
     * Retrieves a list of materials related to a specific unique code.
     *
     * @param string $code The code to filter the materials by chatbot's code.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection The collection of materials as an array.
     */
    public function index(string $code): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $chatBot = ChatBot::where('code', $code)->whereType('widgetChatBot')->first();

        if (!$chatBot) {
            return ChatBotTrainingResource::collection(collect());
        }

        $materials = EmbededResource::with(['metas', 'user', 'childs'])->whereCategory('widgetChatBot')
            ->whereHas('metas', function ($q) use ($code) {
                $q->where('key', 'chatbot_code')->where('value', $code);
            })->orderBy('id', 'desc')->filter()->paginate(preference('row_per_page'));
        return ChatBotTrainingResource::collection($materials);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param string $code
     * @param ChatBotWidgetTrainingRequest $request
     * @return mixed
     */
    public function store(string $code, ChatBotWidgetTrainingRequest $request): mixed
    {
        try {
            $chatBot = $this->chatBotTrainingService->getBotDetails($code);

            if (empty($chatBot)) {
                return response()->json(['error' =>  __('No :x found.', [ 'x' => __('Chatbot') ])], Response::HTTP_NOT_FOUND);
            }

            $collect = $this->chatBotTrainingService->store($code, $request->except('_token'));
            return response()->json(['data' => ChatBotTrainingResource::collection($collect)], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Fetches URLs using the ChatBotFetchRequest data and handles exceptions.
     *
     * @param ChatBotFetchRequest $request The request containing data for fetching URLs.
     * @return JsonResponse The JSON response containing the fetched URLs or error message.
     */
    public function fetchUrl(ChatBotFetchRequest $request): JsonResponse
    {
        try {
            $urls = $this->chatBotTrainingService->fetchUrl($request->except('_token'));
            return response()->json(['data' => $urls], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Trains a material using the ChatBotTrainingService.
     *
     * @param string $code,
     * @param ChatBotWidgetMaterialRequest $request The request containing the material data.
     * @return array|JsonResponse
     */
    public function train(string $code, ChatBotWidgetMaterialRequest $request): array|JsonResponse
    {
        try {
            $chatBot = $this->chatBotTrainingService->getBotDetails($code);

            if ($chatBot->status !== 'Active') {
                return response()->json(['error' => __(':x is not activated.', ['x' => __('Chatbot')])], Response::HTTP_NOT_FOUND);
            }

            if (empty($chatBot)) {
                return response()->json(['error' =>  __('No :x found.', [ 'x' => __('Chatbot') ])], Response::HTTP_NOT_FOUND);
            }

            $checkSubscription = checkUserSubscription('word');

            if ($checkSubscription['status'] != 'success') {
                return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
            }

            request()->merge([
                'embedding_provider' => $chatBot->embedding_provider,
                'embedding_model' => $chatBot->embedding_model,
            ]);
            $service = new ChatBotTrainingService();
            $train = $service->train($request->except('_token'));
            return ChatBotTrainingResource::collection($train)->response()->getData(true);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Destroys a resource based on the provided request.
     *
     * @param Request $request The request containing the resource data.
     * @return JsonResponse The JSON response indicating success or failure of the deletion.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            $this->chatBotTrainingService->delete($request->except('_token'));
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => count($request->id) == 1 ? __('Material') : __('Materials')])], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retrieve and return a collection of ChatBotTrainingResource for a given chatbot code.
     *
     * @param string $code The code of the chatbot to retrieve the related training resources for.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection A collection of ChatBotTrainingResource instances.
     */
    public function csv(string $code): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $chatBot = ChatBot::where('code', $code)->whereType('widgetChatBot')->first();

        if (!$chatBot) {
            return ChatBotTrainingResource::collection(collect());
        }

        $materials = EmbededResource::with(['metas', 'user', 'childs'])->whereCategory('widgetChatBot')
            ->whereHas('metas', function ($q) use ($code) {
                $q->where('key', 'chatbot_code')->where('value', $code);
            })->orderBy('id', 'desc')->get();
        return ChatBotTrainingResource::collection($materials);
    }
}
