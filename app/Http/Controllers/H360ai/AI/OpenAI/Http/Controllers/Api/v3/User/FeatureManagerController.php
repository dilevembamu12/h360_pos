<?php

namespace Modules\OpenAI\Http\Controllers\Api\v3\User;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\v2\FeatureManagerService;

class FeatureManagerController extends Controller
{

    private $featureManagerService;

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the AI provider and chat service.
     */
    public function __construct()
    {
        $this->featureManagerService = new FeatureManagerService();
    }

    /**
     * Retrieves active providers for a specific feature.
     *
     * @param string $featureName The name of the feature.
     * @return JsonResponse The JSON response containing the retrieved active providers.
     */
    public function providers(string $featureName): JsonResponse
    {
        $providers = $this->featureManagerService->getAllProviders($featureName);
        return response()->json(['data' => $providers], Response::HTTP_OK);
    }
}
