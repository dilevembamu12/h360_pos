<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Routing\Controller;
use Illuminate\Http\{
    JsonResponse,
    Response
};
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\OpenAI\Http\Requests\v2\AiChatRequest;
use Modules\OpenAI\Services\v2\ArchiveService;
use Modules\OpenAI\Services\v2\AiChatService;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Transformers\Api\v2\AiChat\{
    ChatDetailsResource,
    ChatResource
};
use Exception, DB;

class AiChatController extends Controller
{

    /**
     * Display a listing of the chat resource.
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return ChatResource::collection(Archive::with('metas')->whereNull('parent_id')->whereNull('type')->orderBy('created_at', 'desc')->get());
    }

    /**
     * Display chat replies for a specific chat.
     *
     * @param  int  $chatId  The ID of the chat.
     * @return \Illuminate\Http\Resources\Json\ResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function show(int $chatId): ResourceCollection|JsonResponse
    {
        $chatReplies = Archive::with(['metas', 'chatbot', 'user', 'user.avatarFile', 'user.metas'])->where('parent_id', $chatId)->whereType('chat_reply')->orderBy('created_at', 'desc')->paginate(preference('row_per_page'));

        if (! $chatReplies->isEmpty()) {
            return ChatDetailsResource::collection($chatReplies);
        }

        return response()->json(['error' => __(':x does not exist.', ['x' => __('Chat')])], Response::HTTP_NOT_FOUND);
    }

    /**
     * Create a new chat.
     *
     * @param  AiChatRequest  $request  The request containing chat data.
     * @return \Illuminate\Http\JsonResponse chat reply content
     */
    public function store(AiChatRequest $request): JsonResponse
    {
        try {
            $chat = (new AiChatService())->store($request->except('_token'));
            return response()->json(['data' => $chat], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()],
                $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Destroy a chat and its replies.
     *
     * @param  int  $chatId  [The ID of the chat to be destroyed.]
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $chatId): JsonResponse
{
    try {
        ArchiveService::delete($chatId, 'chat');
        return response()->json(null, Response::HTTP_NO_CONTENT);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}
