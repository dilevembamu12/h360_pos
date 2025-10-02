<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Services\v2\FeaturePreferenceService;
use Illuminate\Http\{
    JsonResponse,
    Response
};

class FeaturePreferenceController extends Controller
{
    /**
     * Processes feature preference options from the request and returns a JSON response.
     *
     * @param Request $request The request object containing the feature options.
     * @return JsonResponse A JSON response with processed data or an error message.
     */
    public function featureOptions(Request $request): JsonResponse
    {
        try {
            $data = (new FeaturePreferenceService())->processData($request->options);
            return response()->json(['data' => $data], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
