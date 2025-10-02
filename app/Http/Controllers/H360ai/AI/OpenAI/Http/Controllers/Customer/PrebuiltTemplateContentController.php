<?php

/**
 * @package PrebuiltTemplateContentController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 07-10-2024
 */

namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\{
    ContentService
};

use Modules\OpenAI\Services\v2\TemplateService;
use Modules\OpenAI\Services\ChatService;
class PrebuiltTemplateContentController extends Controller
{
    /**
     * Content Service
     *
     * @var object
     */
    protected $contentService;

    /**
     * Content Service
     *
     * @var object
     */
    protected $templateService;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(ContentService $contentService, TemplateService $templateService)
    {
        $this->contentService = $contentService;
        $this->templateService = $templateService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function templates(): \Illuminate\View\View
    {
        $data['useCaseSearchUrl'] = route('user.use_case.search');
        $data['userUseCaseFavorites'] = auth()->user()->use_case_favorites;
        $data['useCases'] = $this->contentService->useCases($data['userUseCaseFavorites']);
        $data['useCaseCategories'] = $this->contentService->useCaseCategories();

        return view('openai::blades.templates', $data);
    }

    /**
     * list of all docs
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function documents(): \Illuminate\View\View
    {
        $service = $this->templateService;
        $data['contents'] = $service->getAll()->paginate(preference('row_per_page'));

        $data['bookmarks'] = auth()->user()->document_bookmarks_openai;
        return view('openai::blades.documents', $data);
    }

    /**
     * list of all favourite docs
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function favouriteDocuments(): \Illuminate\View\View
    {
        $service = $this->templateService;
        $data['contents'] = $service->getAllFavourite()->paginate(preference('row_per_page'));
        $data['bookmarks'] = auth()->user()->document_bookmarks_openai;

        return view('openai::blades.favourite_documents', $data);
    }

    /**
     * @param mixed $slug
     * @param ContentService $contentService
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function template(string $slug): \Illuminate\View\View
    {
        $service = $this->templateService;
        $data['useCases'] = $service->useCases();
        $data['useCase'] = $service->useCasebySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->id);
        $data['slug'] = $slug;
        $data['promtUrl'] = 'api/v2/template';
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        $userId = $this->contentService->getCurrentMemberUserId(null, 'session');
        $data['userId'] = $userId; 
        $data['userSubscription'] = subscription('getUserSubscription',$userId);
        $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);
        $data['aiProviders'] = \AiProviderManager::databaseOptions('templatecontent');

        return view('openai::blades.document', $data);
    }

     /**
     * Content edit
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function editContent($slug): \Illuminate\View\View
    {
        $service = $this->templateService;
        $data['useCases'] = $service->useCases();
        $data['useCase'] = $service->contentBySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->use_case_id);
        $data['accessToken'] = !empty(auth()->user()) ? auth()->user()->createToken('accessToken')->accessToken : '';
        return view('openai::blades.documents-edit', $data);
    }

    /**
     * Update Content
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
    */
    public function updateContent(Request $request)
    {
        return $this->templateService->updateContent($request->contentSlug, $request->content);
    }

    /**
     * Form field by use case
     * @param mixed $slug
     *
     * @return [type]
     */
    public function getFormFiledByUsecase($slug)
    {
        $service = $this->contentService;
        $data['useCase'] = $service->useCasebySlug($slug);
        $data['options'] = $service->getOption($data['useCase']->id);
        return view('openai::blades.form_fields', $data);
    }

     /**
     * Get individual content
     * @param Request $request
     *
     * @return [type]
     */
    public function getContent(Request $request)
    {
        return view('openai::blades.partial-history', $this->contentService->getContent($request->contentId));
    }

    /**
     * delete content
     * @param Request $request
     *
     * @return [type]
     */
    public function deleteContent(Request $request)
    {
        $data = ['total_words', 'balanceReduce', 'slug', 'template_title', 'template_tone', 'template_model', 'template_variant', 'template_language', 'template_level', 'use_case_id', 'template_creator_id', 'user_reply'];

        return (new ChatService())->destroy(request('contentId'), 'template', $data)->getData(true);
    }
    
    /**
     * Download File
     * 
     * @param Request $request
     */
    public function downloadFile(Request $request)
    {
        $fileUrl = str_replace('\\', '/', $request->input('file_url'));

        $fileName = pathinfo($fileUrl, PATHINFO_BASENAME);

        // Download the file
        $contents = file_get_contents($fileUrl);

        // Set appropriate headers for the response
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        return \Response::make($contents, 200, $headers);
    }

}


