<?php
/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 *
 * @created 26-01-2024
 */

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Services\v2\CodeService;
use Modules\OpenAI\Services\v2\ArchiveService;
use Modules\OpenAI\Http\Requests\v2\CodeRequest;
use Modules\OpenAI\Http\Resources\v2\CodeResource;

class CodeController extends Controller
{
    /**
     * The instance of the code service.
     *
     * @var CodeService
     */
    protected $codeService;

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the vector service.
     *
     * @param  CodeService  $codeService
     * 
     * @return void
     */
    public function __construct(CodeService $codeService)
    {
        $this->codeService = $codeService;
    }

    /**
     * Returns a paginated list of code resources.
     * 
     * @param Request $request
     * 
     * @return CodeResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): CodeResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $codes = (new Archive())
            ->with(['metas', 'user', 'childs', 'codeCreator'])
            ->whereHas('metas', function ($q) {
                $q->where('key', 'code_creator_id')->where('value', auth()->id());
            })
            ->where('type', 'code_chat_reply')->whereNull('user_id')
            ->filter('Modules\OpenAI\Filters\CodeFilter')->paginate(preference('row_per_page'));
        
        return CodeResource::collection($codes);
    }

    /**
     * Store a newly created code.
     *
     * @param  CodeRequest  $request
     * 
     * @return JsonResponse
     */
    public function store(CodeRequest $request): array|JsonResponse
    {
        $checkSubscription = checkUserSubscription('word');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();
            $this->codeService->validate($request->validated());
            $id = $this->codeService->prepareData();
                
            if ($id) {
                DB::commit();
                $code = (new Archive())->contentById($id);
                $code = $code->paginate(preference('row_per_page'));
                return CodeResource::collection($code)->response()->getData(true);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $chatbot The ID of the chatbot to display.
     * @return \Illuminate\Http\JsonResponse|CodeResource
     */
    public function show($id): JsonResponse|CodeResource
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }
    
        $history = Archive::where('id', $id)->whereType('code_chat_reply')->first();
    
        if (!$history) {
            return response()->json(['error' => __('No data found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => new CodeResource($history)], Response::HTTP_OK);
    }

    /**
     * Delete a code chat reply by its ID.
     *
     * @param int $id The ID of the code chat reply to delete.
     * @return \Illuminate\Http\JsonResponse|null
     *         The JSON response indicating the success or failure of the deletion operation.
     */
    public function delete($id): \Illuminate\Http\JsonResponse|null
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        try {
            (new CodeService)->delete($id, 'code_chat_reply');
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('code')])], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
