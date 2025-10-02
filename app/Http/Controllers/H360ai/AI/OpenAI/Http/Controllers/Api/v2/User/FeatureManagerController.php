<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
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
        $providers = $this->featureManagerService->getActiveProviders($featureName);
        return response()->json(['data' => $providers], Response::HTTP_OK);
    }

    /**
     * Retrieves models for a specific feature and provider.
     *
     * @param string $featurName The name of the feature.
     * @param string $provider The provider for which models are being retrieved.
     * @return JsonResponse The JSON response containing the retrieved models.
     */
    public function models(string $featurName, string $provider): JsonResponse
    {
        $models = $this->featureManagerService->getModels($featurName, $provider);
        return response()->json(['data' => $models], Response::HTTP_OK);
    }

    /**
     * Retrieves preference for a specific feature and provider based on the request data.
     *
     * @param string $featurName The name of the feature.
     * @param string $provider The provider for which preference is being retrieved.
     * @param Request $request The request object containing data for preference calculation.
     *
     * @return JsonResponse The JSON response containing the retrieved preference.
     */
    public function preference(string $featurName, string $provider, Request $request): JsonResponse
    {
        $preference = $this->featureManagerService->getPreference($featurName, $provider, $request->except('_token'));
        return response()->json(['data' => $preference], Response::HTTP_OK);
    }
    
    public function addiontalOptions(string $featureName, string $providerName, string $modelName): JsonResponse
    {
        $models = $this->featureManagerService->getAdditionalOptions($featureName, $providerName, $modelName);
        return response()->json(['data' => $models], Response::HTTP_OK);
    }
}
