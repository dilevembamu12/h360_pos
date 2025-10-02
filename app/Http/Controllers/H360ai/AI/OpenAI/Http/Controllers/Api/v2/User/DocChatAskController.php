<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use App\Http\Controllers\Controller;
use Modules\OpenAI\Services\v2\DocChatAskService;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Modules\OpenAI\Http\Requests\EmbedQuestionRequest;
use Modules\OpenAI\Transformers\Api\v2\AiDocChat\DocChatReplyResource;

class DocChatAskController extends Controller
{
    /**
     * Ask a question related to an embedded resource.
     *
     * @param  EmbedQuestionRequest  $request
     * 
     * @return DocChatReplyResource|JsonResponse
     */
    public function askQuestion(EmbedQuestionRequest $request): DocChatReplyResource|JsonResponse
    {
        $checkSubscription = checkUserSubscription('word');
        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        if (isset($request->file_id) && is_array($request->file_id)) {
            $data = (new DocChatAskService)->getProviderModel($request->file_id);

            if (is_null($data)) {
                return response()->json(['error' => __('Something went wrong. Please try once again')], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            request()->merge([
                'embedding_provider' => $data['provider'],
                'embedding_model' => $data['model'],
            ]);
        }

        try {
            return new DocChatReplyResource(json_decode((new DocChatAskService())->askQuestion($request->except('_token'))));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
