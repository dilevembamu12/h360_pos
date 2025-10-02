<?php
/**
 * @package AIController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\OpenAI\Services\{
    ContentService,
    ImageService,
};

use Modules\OpenAI\Entities\{
    ChatBot,
    ContentTypeMeta
};
use App\Models\{
    Preference,
};
use Modules\OpenAI\Http\Requests\{
    AiPreferenceRequest
};
use Modules\OpenAI\DataTables\{
    ContentDataTable,
    ImageDataTable
};
use App\Lib\Env;
use Illuminate\Support\Facades\Session;
use Infoamin\Installer\Helpers\PermissionsChecker;

class OpenAIController extends Controller
{
    /**
     * Content Service
     *
     * @var object
     */
    protected $contentService;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }
    /**
     * Display a listing of the resource.
     *
     * @param  ContentDataTable  $dataTable
     * @return mixed
     */

    /**
     * Images
     *
     * @param ImageDataTable $dataTable
     * @return mixed
     */
    public function images(ImageDataTable $dataTable)
    {
        $data['users'] = $this->contentService->users();
        $data['sizes'] = config('openAI.size');
        return $dataTable->render('openai::admin.image.index', $data);
    }

    /**
     * Content edit
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function editContent($slug)
    {
        $service = $this->contentService;
        $data['useCases'] = $service->useCases();
        $data['useCase'] = $service->contentBySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->use_case_id);
        $data['slug'] = $slug;
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        
        return view('openai::blades.documents-edit', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function contentPreferences(ImageService $imageService)
    {
        $data['preferences'] = $this->contentService->features();
        $data['languages'] = $this->contentService->languages();
        $data['meta'] = $this->contentService->getAllMeta();
        $data['omitTextToSpeechLanguages'] = moduleConfig('openai.text_to_speech_language');
        $data['openai'] = preference();
        $data['userPermission'] = isset($data['openai']['user_permission']) ? json_decode($data['openai']['user_permission']) : null;

        $data['aiModels'] = config('openAI.openAIModel');
        $data['aiModelDescription'] = config('openAI.modelDescription');
        return view('openai::admin.preference.index', $data);
    }

    /**
     * Create Content preference
     *
     * @param Request $request
     * @param
     */
    public function createContentPreferences(AiPreferenceRequest $request, ChatBot $chatBot, PermissionsChecker $checker)
    {
        $data = $this->contentService->storeMeta($request->meta);

        $post = $request->only('short_desc_length', 'long_desc_length', 'long_desc_length', 'ai_model', 'max_token_length', 'bad_words', 'stable_diffusion_engine', 'openai_engine', 'conversation_limit', 'word_count_method');

        $checkPermission = $checker->checkPermission([".env" => 666]);
        if ($checkPermission['permissions'][0]['isActive'] == false) {
            $data = ['status' => 'fail', 'message' => __('Please give write permission to :x file in root folder.', ['x' => '.env'])];
            Session::flash($data['status'], $data['message']);
            return redirect()->route('admin.features.preferences');
        }
        $aiKeys = $request->apiKey;
        foreach($aiKeys as $key => $value) {
            Env::set(strtoupper($key), $value ? $value : false);
        }
        $post['bad_words'] = $request->bad_words ?? '';
        $i = 0;
        $response=[];

        foreach ($post as $key => $value) {
            if( $key === 'bad_words' || !empty($value)) {
                $response[$i]['category'] = 'openai';
                $response[$i]['field']    = $key;
                $response[$i]['value']    = $value;
                $i++;
            }
        }

        $permission = $request->only('hide_template', 'hide_image', 'hide_code', 'hide_speech_to_text', 'hide_text_to_speech', 'hide_long_article', 'hide_chat', 'hide_plagiarism', 'hide_aichatbot', 'hide_ai_detector');
        foreach ($permission as $key => $value) {
            $permissions[$key] = $value;
        }
        $permissions = $request->user_access;
        $response[$i] = ['category' => 'openai', 'field' => 'user_permission', 'value' => json_encode($permissions)];
        
        foreach ($response as $key => $value) {
            if (Preference::storeOrUpdate($value)) {
                $data = ['status' => 'success', 'message' => __('The :x has been successfully saved.', ['x' => __('AI Preference Settings')])];
            }
        }

        Session::flash($data['status'], $data['message']);
        return back();
    }
}
