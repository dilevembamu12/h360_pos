<?php
/**
 * @package ImageController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\OpenAI\Services\CodeService;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Services\ChatService;

class CodeController extends Controller
{
    /**
     * Code Service
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
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data['codes'] = (new Archive())
            ->with(['metas', 'user', 'childs', 'codeCreator'])
            ->whereHas('metas', function ($q) {
                $q->where('key', 'code_creator_id')->where('value', auth()->id());
            })
            ->where('type', 'code_chat_reply')->latest()->paginate(preference('row_per_page'));
        return view('openai::blades.codes.list', $data);
    }

    /**
     * code view using slug
     *
     * @param mixed $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function view($slug)
    {
        $response = ['status' => 'error', 'message' => __('The data you are looking for is not found.')];
        $data['code'] = (new Archive)->contentBySlug($slug, 'code')->first();
        
        if (empty($data['code'])) {
            \Session::flash($response['status'], $response['message']);
            return redirect()->route('user.codeList');
        }
        $data['meta'] = $this->codeService->getMeta('code_writer');
        return view('openai::blades.codes.view', $data);
    }

    /**
     * Delete code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        $data = ['user_reply', 'code_creator_id', 'formated_code', 'slug', 'code', 'balanceReduce', 'total_words', 'code_level', 'code_language', 'code_title', 'balanceReduce'];

        return (new ChatService())->destroy(request('id'), 'code', $data);
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
}


