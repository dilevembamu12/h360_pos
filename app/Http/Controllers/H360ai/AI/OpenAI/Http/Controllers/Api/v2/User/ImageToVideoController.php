<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;


use Illuminate\Http\Request;
use Modules\OpenAI\Entities\Archive;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\OpenAI\Services\v2\ImageToVideoService;
use Modules\OpenAI\Transformers\Api\v2\AiVideo\AiVideoDetailsResource;
use Illuminate\Http\{
    JsonResponse,
    Response
};
use Modules\OpenAI\Http\Requests\v2\VideoRequest;

class ImageToVideoController extends Controller
{

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the vector service.
     *
     * @param  ImageToVideoService $imageToVideoService
     * 
     * @return void
     */
    public function __construct(
        protected ImageToVideoService $imageToVideoService,
    ) {}

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(VideoRequest $request)
    {
        $checkSubscription = checkUserSubscription('video');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        $this->imageToVideoService->validation();
        $optionRequest['options'] = $request->except(['_token', 'file', 'provider']);
        $requestData = array_merge($request->only(['file', 'provider']), $optionRequest);

        try {
            $info = $this->imageToVideoService->generate($requestData);
            $code = $this->imageToVideoService->fetchVideo($info['video_id']);
            $data['userSubscription'] = subscription('getUserSubscription',auth()->user()->id);
            $data['featureLimit'] = subscription('getActiveFeature', $data['userSubscription']?->id ?? 1);
            $data['featureLimit']['video']['reduce'] = $info['balanceReduce'];

            return response()->json(['data' => new AiVideoDetailsResource(collect(['video' => $code, 'balance' => $data['featureLimit']['video']]))], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 
                $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (! is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        \DB::beginTransaction();
        try {
            $this->imageToVideoService->delete($id);
            \DB::commit();
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('AI Video')])], Response::HTTP_OK);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
