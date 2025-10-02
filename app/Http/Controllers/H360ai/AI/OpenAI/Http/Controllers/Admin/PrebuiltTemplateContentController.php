<?php
/**
 * @package PrebuiltTemplateContentController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 07-10-2024
 */
namespace Modules\OpenAI\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\OpenAI\Services\{
    ContentService
};

use Modules\OpenAI\Entities\{
    UseCase
};

use Modules\OpenAI\Http\Requests\{
    ContentUpdateRequest
};
use Modules\OpenAI\DataTables\{
    ContentDataTable
};

use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Modules\OpenAI\Exports\ContentExport;
use Modules\OpenAI\Services\v2\FeatureManagerService;
use Modules\OpenAI\Services\v2\TemplateService;
use Modules\OpenAI\Entities\Archive;

class PrebuiltTemplateContentController extends Controller
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
     * Display the index page with content data table.
     *
     * @param  ContentDataTable  $dataTable The data table instance for content.
     * @return \Illuminate\Http\view The response containing the rendered view with data.
     */
    public function index(ContentDataTable $dataTable)
    {
        $models = [];
        $featureManagerService = new FeatureManagerService();
        $providers = $featureManagerService->getActiveProviders('templatecontent');
        foreach($providers as $provider) {
            $model[] = $featureManagerService->getModels('templatecontent', $provider);
            $models = array_merge(...array_values($model));
        }

        $data['useCases'] = UseCase::where(['status' => 'Active'])->get();
        $data['languages'] = $this->contentService->languages();
        $data['omitLanguages'] = moduleConfig('openai.language');
        $data['users'] = $this->contentService->users();
        $data['aiModel'] = $models;
        return $dataTable->render('openai::admin.content.index', $data);
    }

    /**
     * Show the form for editing the specified content.
     *
     * @param  string  $slug The slug of the content to be edited.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse The view for editing the content or a redirect response if content is not found.
     */
    public function edit(string $slug)
    {
        $data = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Content')])];
        $data['content'] = (new TemplateService)->contentBySlug($slug);
        if (empty($data['content'])) {
            Session::flash($data['status'], $data['message']);
            return redirect()->back();
        }
        $data['readonly'] = is_null($data['content']->parent_id) ? '' : 'readonly';
        $data['disabled'] = is_null($data['content']->parent_id) ? '' : 'disabled';
        $data['categories'] = $this->contentService->useCases();
        $data['contentVersion'] = $this->contentService->model()->where('parent_id', $data['content']->id)->get();
        return view('openai::admin.content.edit', $data);
    }

   /**
     * Update the specified content.
     *
     * @param  \App\Http\Requests\ContentUpdateRequest  $request The request containing the content update data.
     * @return \Illuminate\Http\RedirectResponse The redirect response after attempting to update the content.
     */
    public function update(ContentUpdateRequest $request)
    {
        $data = (new TemplateService)->updateContent($request->slug, $request->content)->getData(true);

        Session::flash($data['status'], $data['message']);
        return redirect()->route('admin.features.contents');
    }

    /**
     * Content edit
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function editContent($slug): \Illuminate\View\View
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
     * Delete content
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $delete = (new TemplateService)->delete($request->contentId)->getData(true);
        Session::flash($delete['status'], $delete['message']);

        return redirect()->back();
    }
    
    /**
     * Content list pdf
     *
     * @return mixed
     */
    public function pdf()
    {
        $data['contents'] = Archive::with(['metas', 'user', 'useCase:id,name', 'templateCreator'])->whereType('template')->get();

        return printPDF($data, 'content_list_' . time() . '.pdf', 'openai::admin.content.content_list_pdf', view('openai::admin.content.content_list_pdf', $data), 'pdf');
    }

    /**
     * Content list csv
     *
     * @return mixed
     */
    public function csv()
    {
        return Excel::download(new ContentExport(), 'content_list_' . time() . '.csv');
    }
}
