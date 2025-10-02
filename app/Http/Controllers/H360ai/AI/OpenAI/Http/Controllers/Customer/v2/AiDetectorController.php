<?php

namespace Modules\OpenAI\Http\Controllers\Customer\v2;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\v2\FeaturePreferenceService;

class AiDetectorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function template()
    {
        $data['aiProviders'] = \AiProviderManager::databaseOptions('aidetector');
        $data['featurePreferecnes'] = (new FeaturePreferenceService())->processData('ai_detector');
        $data['promtUrl'] = '/api/v2/aidetector';
        $data['userSubscription'] = subscription('getUserSubscription',auth()->user()->id);
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);
        return view('openai::blades.ai_detector.index', $data);
    }
}
