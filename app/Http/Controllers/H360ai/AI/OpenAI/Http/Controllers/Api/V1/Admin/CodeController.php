<?php
/**
 * @package CodeController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Api\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Http\Resources\CodeResource;
use Modules\OpenAI\Services\codeService;

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
     * Image list
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $configs        = $this->initialize([], $request->all());
        $images = $this->codeService->model();
        if (count(request()->query()) > 0) {
            $images = $images->filter();
        }

        $contents = $images->with(['User:id,name'])->paginate($configs['rows_per_page']);
        return $this->response(CodeResource::collection($contents)->response()->getData(true));
    }

    /**
     * View image
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function view($slug)
    {
        $code = $this->codeService->codeBySlug($slug);
        return !empty($code) ? $this->okResponse(new CodeResource($code)) : $this->notFoundResponse([], );
    }

    /**
     * Delete image
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        return $this->codeService->delete($id) ? $this->okResponse([], __('Code Deleted Successfully')) : $this->notFoundResponse([], );
    }
}


