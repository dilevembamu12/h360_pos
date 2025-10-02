<?php

namespace Modules\OpenAI\Http\Controllers\Customer\v2;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\ContentService;

class ImageToVideoController extends Controller
{

    /**
     * Display the template view for the image-to-video feature.
     *
     * @return \Illuminate\View\View The view instance for the image-to-video template.
     */
    public function template()
    {
        $data['aiProviders'] = \AiProviderManager::databaseOptions('videomaker');
        $userId = (new ContentService())->getCurrentMemberUserId(null, 'session');
        $data['userId'] = $userId; 
        $data['userSubscription'] = subscription('getUserSubscription', $userId);
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);
        return view('openai::blades.img-to-video.img_to_video', $data);
    }
}
