<?php
/**
 * @package ImageController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\CodeService;
use Modules\OpenAI\DataTables\{
    CodeDataTable
};
use Maatwebsite\Excel\Facades\Excel;
use Modules\OpenAI\Exports\CodeExport;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Services\ChatService;
use Illuminate\Http\Response;

class CodeController extends Controller
{

    /**
     * Service
     *
     * @var object
     */
    protected $codeService;

    /**
     * @param codeService $codeService
     */
    public function __construct(CodeService $codeService)
    {
        $this->codeService = $codeService;
    }

     /**
     * List view of code
     *
     * @param CodeDataTable $codeDataTable
     * @return mixed
     */
    public function index(CodeDataTable $codeDataTable)
    {
        $data['users'] = $this->codeService->users();
        return $codeDataTable->render('openai::admin.code.index', $data);
    }

    /**
     * View code
     * @param mixed $slug
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function view($slug)
    {
        $data['code'] = (new Archive)->contentBySlug($slug, 'code')->first();

        return view('openai::admin.code.view', $data);
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $data = ['user_reply', 'code_creator_id', 'formated_code', 'slug', 'code', 'balanceReduce', 'total_words', 'code_level', 'code_language', 'code_title', 'balanceReduce'];
        
        $delete =  (new ChatService())->destroy(request('codeId'), 'code', $data);

        if ($delete->getStatusCode() === Response::HTTP_NO_CONTENT) {
            return redirect()->back()->with('success', __('The :x has been successfully deleted.', ['x' => __('Code')]));
        }
    
        return redirect()->back()->with('fail', __('Failed to delete code. Please try again.'));
    }

    /**
     * Store Image via service
     * @param Request $request
     *
     * @return [type]
     */
    public function saveCode($code)
    {
        return $this->codeService->save($code);
    }

    /**
     * Code To Text list csv
     *
     * @return void
     */
    public function csv()
    {
        return Excel::download(new CodeExport(), 'code_list_' . time() . '.csv');
    }
    
    /**
     * Code To Text list pdf
     *
     * @return void
     */
    public function pdf()
    {
        $data['codes'] = (new Archive)->codes('code')->get();

        return printPDF($data, 'code_list_' . time() . '.pdf', 'openai::admin.code.code_list_pdf', view('openai::admin.code.code_list_pdf', $data), 'pdf');
    }
}


