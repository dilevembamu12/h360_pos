<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\v2\SpeechToTextService;
use Illuminate\Http\Response;
use Modules\OpenAI\Transformers\Api\v2\SpeechToText\SpeechToTextDetailsResource;
use Exception;

class SpeechToTextController extends Controller
{
    /**
     * @param SpeechToTextService $contentService
     */
    public function __construct(
        protected SpeechToTextService $speechToTextService
        ) {}

    /**
     * Convert speech to text.
     *
     * @param  Request  $request
     * @param  SpeechToTextService  $speechToTextService
     * @return mixed
     */
    public function generate(Request $request): mixed
    {
        $checkSubscription = checkUserSubscription('minute');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        try {
            $response = $this->speechToTextService->handleSpeechGenerate($request->except('_token'));
            return new SpeechToTextDetailsResource($response);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
