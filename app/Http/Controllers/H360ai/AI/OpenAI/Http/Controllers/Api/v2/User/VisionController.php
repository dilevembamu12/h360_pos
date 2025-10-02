<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Modules\OpenAI\Transformers\Api\v2\{
    ChatDetailsResource,
    ChatResource
};
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\OpenAI\Http\Requests\v2\VisionRequest;
use Modules\OpenAI\Http\Requests\v2\VisionUpdateRequest;
use Modules\OpenAI\Services\v2\VisionService;
use Modules\OpenAI\Entities\Archive;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Http\Resources\ChatConversationResource;
use Illuminate\Http\{
    JsonResponse,
    Response
};
use ArchiveService;
use Exception, DB;
use Illuminate\Http\Request;

class VisionController extends Controller
{
    /**
     * Constructor method.
     * 
     * @param VisionService $visionService
     */
    public function __construct(
        protected VisionService $visionService
        ) {}

    /**
     * Display a listing of the chat resource.
     *
     *  @return ChatConversationResource|JsonResponse
     */
    public function index(): ChatConversationResource|JsonResponse
    {
        $chatConversation = Archive::with(['metas', 'user:id,name'])->whereType('vision_chat')->orderBy('id', 'desc')->paginate(preference('row_per_page'));
        return response()->json(ChatConversationResource::collection($chatConversation)->response()->getData(true));
    }

    /**
     * Display chat replies for a specific chat.
     *
     * @param  int  $chatId  The ID of the chat.
     * @return ResourceCollection|JsonResponse
     */
    public function show($chatId): ResourceCollection|JsonResponse
    {
        if (! is_numeric($chatId)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        $chatReplies = Archive::with(['metas', 'user:id,name'])->where('parent_id', $chatId)->whereType('vision_chat_reply')->orderBy('id', 'asc')->paginate(preference('row_per_page'));
        if (! $chatReplies->isEmpty()) {
            return ChatDetailsResource::collection($chatReplies);
        }

        return response()->json(['error' => __('The :x does not exist.', ['x' => __('Vision Conversation')])], Response::HTTP_NOT_FOUND);
    }

    /**
     * Create a new vision chat.
     *
     * @param  VisionRequest $request  The request containing chat data.
     * @return JsonResponse chat reply content
     */
    public function store(VisionRequest $request): JsonResponse
    {
        $checkSubscription = checkUserSubscription('word');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }
        
        try {
            $vision = $this->visionService->store($request->except('_token'));
            return response()->json(['data' => $vision], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Destroy a vision chat and its replies.
     *
     * @param  int  $chatId  [The ID of the chat to be destroyed.]
     * @return JsonResponse
     */
    public function destroy($chatId): JsonResponse
    {
        if (! is_numeric($chatId)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();
        try {
            $this->visionService->delete($chatId);
            DB::commit();
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('Vision Chat')])], Response::HTTP_OK);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a conversation title
     * @param Request $request
     *
     * @return [type]
     */
    public function update($chatId, VisionUpdateRequest $request)
    {
        if (! is_numeric($chatId)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        $request->merge([
            'chatId' => $chatId
        ]);

        try {
            $vision = $this->visionService->update($request->except('_token'));
            return response()->json(['data' => $vision], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

    }
}
