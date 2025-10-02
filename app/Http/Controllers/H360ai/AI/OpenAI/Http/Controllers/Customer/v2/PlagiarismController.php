<?php

namespace Modules\OpenAI\Http\Controllers\Customer\v2;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;

class PlagiarismController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function template()
    {   
        $data['aiProviders'] = \AiProviderManager::databaseOptions('plagiarism');
        $data['promtUrl'] = '/api/v2/plagiarism';
        $data['userSubscription'] = subscription('getUserSubscription',auth()->user()->id);
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);
        return view('openai::blades.plagiarism.index', $data);
    }
}
