<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\OpenAI\Services\v2\{
    AiDocChatService
};
use Illuminate\Support\Facades\DB;
use Modules\OpenAI\Http\Resources\{
    EmbedFileResource,
    EmbedResource,
};
use Modules\OpenAI\Http\Requests\EmbedRequest;
use Illuminate\Http\Response;


class AiDocChatController extends Controller
{
    /**
     * Display a listing of embedded resources.
     *
     * @param  Request  $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $contents = (new AiDocChatService())->files()->filter('Modules\OpenAI\Filters\EmbededResourceFilter');

        if (auth('api')->user()->role()->type !== 'admin') {
            $contents = $contents->where('user_id', auth('api')->user()->id);
        }

        $contents = $contents->whereNull('parent_id')->orderBy('id', 'desc')->paginate($configs['rows_per_page']);

        return EmbedResource::collection($contents)->response()->getData(true);
    }

    /**
     * Display the specified embedded resource.
     *
     * @param int $id
     * 
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $contents = (new AiDocChatService())->contentById($id);
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
    public function delete(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            if (!is_numeric($id)) {
                return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
            }

            $deleted = (new AiDocChatService())->delete($id);
        
            if ($deleted) {
                DB::commit();
                return response()->json(null, Response::HTTP_NO_CONTENT);
            } else {
                DB::rollBack();
                return response()->json(['error' => __('No Data found.')], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => __('An error occurred.')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        if ($request->provider && $request->model) {
            request()->merge([
                'provider' => $request->provider,
                'model' => $request->model
            ]);
        }

        $checkSubscription = checkUserSubscription('word');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }
        $fileIds = [];
        try {
            DB::beginTransaction();

            $fileIds = (new AiDocChatService())->extractedFileIds($request->except('_token'));

            if (!is_array($fileIds)) {
                $fileIds = array($fileIds);
            }

            if ($fileIds) {
                DB::commit();
                $contents = (new AiDocChatService())->contents($fileIds);
                
                return EmbedResource::collection($contents)->response()->getData(true);
            }

            return response()->json(['error' => __('There was a problem with your file.'), Response::HTTP_NOT_FOUND]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
