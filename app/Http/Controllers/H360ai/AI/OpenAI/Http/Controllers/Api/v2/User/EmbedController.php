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
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\OpenAI\Services\{
    VectorService
};
use Illuminate\Support\Facades\DB;
use Modules\OpenAI\Http\Resources\{
    EmbedFileResource,
    EmbedResource,
    BotReplyResource
};
use Modules\OpenAI\Http\Requests\EmbedRequest;
use Modules\OpenAI\Http\Requests\EmbedQuestionRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class EmbedController extends Controller
{
    /**
     * The instance of the vector service.
     *
     * @var VectorService
     */
    protected $vectorService;

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the vector service.
     *
     * @param  VectorService  $vectorService
     * 
     * @return void
     */
    public function __construct(VectorService $vectorService)
    {
        $this->vectorService = $vectorService;
    }

    /**
     * Display a listing of embedded resources.
     *
     * @param  Request  $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $configs        = $this->initialize([], $request->all());
        $vectorService = $this->vectorService;
        $contents = $vectorService->files();

        if (auth('api')->user()->role()->type !== 'admin') {
            $contents = $contents->where('user_id', auth('api')->user()->id);
        }
        
        if (request('type')) {
            $contents = $contents->where('type', request('type'));
        }

        if (count(request()->query()) > 0) {
            $contents = $contents->filter();
        }

        $contents = $contents->whereNull('parent_id')->orderBy('id', 'desc')->paginate($configs['rows_per_page']);

        return EmbedResource::collection($contents)->response()->getData(true);
    }

    /**
     * Display the specified embedded resource.
     *
     * @param  mixed  $id
     * 
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $contents = $this->vectorService->contentById($id);
        if ($contents) {
            return response()->json(['data' => new EmbedFileResource($contents)], Response::HTTP_OK);
        }

        return response()->json(['error' => __('No Data found.')], Response::HTTP_NOT_FOUND);
    }

    /**
     * Delete the specified embedded resource.
     *
     * @param  int  $id
     * 
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        if (! is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        return $this->vectorService->delete($id) 
            ? response()->json(null, Response::HTTP_NO_CONTENT)
            : response()->json(['error' => __('No Data found.')], Response::HTTP_NOT_FOUND);
    }

    /**
     * Store a newly created embedded resource.
     *
     * @param  EmbedRequest  $request
     * 
     * @return JsonResponse
     */
    public function store(EmbedRequest $request)
    {
        $checkSubscription = checkUserSubscription('word');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        try {
            DB::beginTransaction();
            $this->vectorService->validate($request->validated());
            $fileIds = $this->vectorService->extractor();

            if (!is_array($fileIds)) {
                $fileIds = array($fileIds);
            }

            if ($fileIds) {
                DB::commit();
                $contents = $this->vectorService->contents($fileIds);
                
                return EmbedResource::collection($contents)->response()->getData(true);
            }

            return response()->json(['error' => __('There was a problem with your file.'), Response::HTTP_NOT_FOUND]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Ask a question related to an embedded resource.
     *
     * @param  EmbedQuestionRequest  $request
     * 
     * @return JsonResource
     */
    public function askQuestion(EmbedQuestionRequest $request)
    {
        $checkSubscription = checkUserSubscription('word');
        
        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        try {
            $this->vectorService->validate($request->validated());
            return new BotReplyResource(json_decode($this->vectorService->askQuestion()));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
