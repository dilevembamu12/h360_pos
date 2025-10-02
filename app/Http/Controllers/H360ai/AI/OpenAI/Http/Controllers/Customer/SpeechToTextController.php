<?php

namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\{
    ContentService
};

use Modules\OpenAI\Services\v2\SpeechToTextService;

use Modules\OpenAI\Entities\Archive;

class SpeechToTextController extends Controller
{
    protected $speechToTextService, $contentService;

    public function __construct(SpeechToTextService $speechToTextService, ContentService $contentService)
    {
        $this->speechToTextService = $speechToTextService;
        $this->contentService = $contentService;
    }

    /**
     * Retrieves and displays a paginated list of all speech records.
     *
     * @return \Illuminate\View\View  The view for displaying the paginated list of speeches.
     */
    public function index() 
    {
        $data['speeches'] =  (new Archive)->speeches('speech_to_text')->paginate(preference('row_per_page'));
        return view('openai::blades.speeches.speech-history', $data);
    }

    /**
     * Generates the data required for rendering the speech template view.
     *
     * @return \Illuminate\View\View  The view for rendering the speech template.
     */
    public function template() 
    {
        $data['aiProviders'] = \AiProviderManager::databaseOptions('speechtotext');

        $data['promtUrl'] = 'api/v2/speeches';
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        $data['userId'] = $this->contentService->getCurrentMemberUserId(null, 'session');
        $data['userSubscription'] = subscription('getUserSubscription');
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);
        return view('openai::blades.speeches.speech', $data);
    }

    /**
     * Displays the edit form for a specific speech record.
     *
     * @param string $value  The encrypted speech ID.
     * @return \Illuminate\View\View  The view for editing the speech record.
     */
    public function edit(string $value)
    {
        $id =  techDecrypt($value);
        $service = $this->speechToTextService;
        $data['speech'] = $service->speechById($id);
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        return view('openai::blades.speeches.speech-edit', $data);
    }
 
    /**
     * Updates a speech record with the provided content.
     *
     * @param \Illuminate\Http\Request $request  The request object containing the speech ID and content.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response  The response after the update operation.
     */
    public function update(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
    {
        return $this->speechToTextService->updateSpeech($request->id, $request->content);
    }
 
    /**
     * Deletes a speech record based on the provided speech ID.
     *
     * @param \Illuminate\Http\Request $request  The request object containing the speech ID.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response  The response after deletion.
     */
    public function delete(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
    {
        return $this->speechToTextService->delete($request->speechId);
    }

}
