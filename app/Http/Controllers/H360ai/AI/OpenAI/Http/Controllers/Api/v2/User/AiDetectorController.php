<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\v2\AiDetectorService;
use Illuminate\Http\{
    Response
};

class AiDetectorController extends Controller
{
    /**
     * @param AiDetectorService $aiDetectorService
     */

     public function __construct(
        protected AiDetectorService $aiDetectorService
    ) {}

    /**
     * Generate a plagiarism check.
     *
     * @param Request $request The request containing plagiarism data.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $aiDetectorStoreRequest)
    {
        $checkSubscription = checkUserSubscription('page');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }
        
        try {
            $response = $this->aiDetectorService->handleAiDetector($aiDetectorStoreRequest->except('_token'));
            return response()->json(['data' => $response], Response::HTTP_CREATED);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
