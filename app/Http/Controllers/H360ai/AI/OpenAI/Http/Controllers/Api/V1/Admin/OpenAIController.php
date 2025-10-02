<?php

/**
 * @package OpenAIController for Admin
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 26-03-2023
 */

namespace Modules\OpenAI\Http\Controllers\Api\V1\Admin;

use Exception;
use App\Traits\ModelTraits\Filterable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Http\Controllers\Admin\{
    ImageController,
    CodeController
};

use Modules\OpenAI\Http\Resources\ContentResource;
use Modules\OpenAI\Services\{
    UseCaseTemplateService,
    ContentService,
    ImageService,
    CodeService
};
use Modules\OpenAI\Entities\{
    OpenAI
};

class OpenAIController extends Controller
{
    /**
     * Use Filtable trait.
     */
    use Filterable;

    /**
     * Content Service
     *
     * @var object
     */
    protected $contentService;

    /**
     * Constructor
     *
     * @param ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * List of all content
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $configs        = $this->initialize([], $request->all());
        $contentServices = $this->contentService;
        $contents = $contentServices->model()->orderBy('id', 'DESC');
        if (count(request()->query()) > 0) {
            $contents = $contents->filter();
        }

        $contents = $contents->with(['useCase:slug,creator_id', 'User:id,name'])->paginate($configs['rows_per_page']);
        $responseData = ContentResource::collection($contents)->response()->getData(true);
        return $this->response($responseData);
    }

    /**
     * Content view
     *
     * @param mixed $slug
     * @return JsonResponse
     */
    public function view($slug)
    {
        $contents = $this->contentService->contentBySlug($slug);
        if ($contents) {
            return $this->okResponse(new ContentResource($contents));
        }

        return $this->notFoundResponse([], __('No :x found.', ['x' => __('Content')]));
    }

    /**
     * Content version
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $request = app('Modules\OpenAI\Http\Requests\ContentUpdateRequest')->safe();
        $contents = $this->contentService->model();
        $content = $contents->where('id', $request->id)->whereNull('parent_id')->first();
        if (empty($content)) {
            return $this->notFoundResponse([], __('No :x found.', ['x' => __('Content')]));
        }

        if ($this->contentService->createVersion($content, $request->all())) {
            $content = $this->contentService->model()->where('parent_id', $request->id)->latest()->first();
            return $this->createdResponse(new ContentResource($content), __('Content Created successfully!'));
        }
    }

    /**
     * Content delete
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        if (!is_numeric($id)) {
            return $this->forbiddenResponse([], __('Invalid Request!'));
        }

        $delete =  json_decode(json_encode($this->contentService->delete($id)));
        return $delete->original->status == 'fail' ? $this->badRequestResponse([], __($delete->original->message)) : $this->okResponse([], __($delete->original->message));
    }
    /**
     * Ask to the API
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ask(Request $request)
    {
        $request = app('Modules\OpenAI\Http\Requests\ContentStoreRequest')->safe();
        $useCase = $this->contentService->useCasebySlug($request->useCase);
        $templateService = new UseCaseTemplateService($useCase->prompt);
        $templateService->setVariables(json_decode($request->questions, true));
        try {
            $result = OpenAI::completions([
                'prompt' => $templateService->render() .' '. 'The writting language must be in '.  $request->language. ' '. 'and please keep the tone ' .' '. $request->tone,
                'temperature' => (float) $request->temperature,
                'n' => (int) $request->variant,
            ]);
            if ($result) {
                $response = $this->contentService->prepareData($request->all(), $useCase->id, $templateService->render(), $result);
                return $this->successResponse($response);
            }

        } catch (Exception $e) {
            $response = $e->getMessage();
            $data = [
                'response' => $response,
                'status' => 'failed',
            ];
            return $this->unprocessableResponse($data);
        }

    }

    /**
     * Image creation from promt
     *
     * @param Request $request
     * @param ImageService $imageService
     * @return JsonResponse
     */
    public function image(Request $request, ImageService $imageService)
    {
        try {
            $imageUrls = $imageService->createImage($request->all());
            if (isset($imageUrls['status']) && $imageUrls['status'] == 'error') {
                return $this->unprocessableResponse($imageUrls);
            } else {
                return $imageUrls;
            }
        } catch(Exception $e) {
            $response = $e->getMessage();
            $data = [
                'response' => $response,
                'status' => 'failed',
            ];
            return $this->unprocessableResponse($data);
        }
    }

    /**
     * Code creation from promt
     * @param Request $request
     * @param CodeService $codeService
     *
     * @return [type]
     */
    public function code(Request $request, CodeService $codeService, CodeController $codeController)
    {
        try {
            $code = $codeService->createCode($request->all());
            return $this->successResponse($codeController->saveCode($code));
        } catch(Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

    }


}


