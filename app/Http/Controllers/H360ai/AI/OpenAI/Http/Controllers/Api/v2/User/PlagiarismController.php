<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\user;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\OpenAI\Services\v2\PlagiarismService;
use Modules\OpenAI\Http\Requests\v2\PlagiarismStoreRequest;

use Modules\OpenAI\Services\ContentService;

class PlagiarismController extends Controller
{
    /**
     * @param PlagiarismService $plagiarismService
     */

    public function __construct(
        protected PlagiarismService $plagiarismService
    ) {}

    /**
     * Generate a plagiarism check.
     *
     * @param PlagiarismStoreRequest $plagiarismStoreRequest The request containing plagiarism data.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(PlagiarismStoreRequest $plagiarismStoreRequest)
    {
        $checkSubscription = checkUserSubscription('page');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }
        
        try {
            $response = $this->plagiarismService->handlePlagiarism($plagiarismStoreRequest->except('_token'));
            return response()->json(['data' => $response], Response::HTTP_CREATED);
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
