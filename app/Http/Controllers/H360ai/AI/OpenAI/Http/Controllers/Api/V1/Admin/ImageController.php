<?php
/**
 * @package ImageController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 06-03-2023
 */
namespace Modules\OpenAI\Http\Controllers\Api\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ModelTraits\Filterable;
use Modules\OpenAI\Services\ImageService;
use Modules\OpenAI\Http\Resources\ImageResource;

class ImageController extends Controller
{

    /**
     * Use Filterable trait.
     */
    use Filterable;

    /**
     * Image Service
     *
     * @var object
     */
    protected $imageService;

    /**
     * Constructor
     *
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Store Image via service
     * @param Request $request
     *
     * @return [type]
     */
    public function saveImage($imageUrls)
    {
        return $this->imageService->save($imageUrls);
    }

    /**
     * Image list
     * @return [type]
     */
    public function index(Request $request)
    {
        $configs        = $this->initialize([], $request->all());
        $images = $this->imageService->model();
        if (count(request()->query()) > 0) {
            $images = $images->filter();
        }

        $contents = $images->with(['User:id,name'])->paginate($configs['rows_per_page']);
        $responseData = ImageResource::collection($contents)->response()->getData(true);
        return $this->response($responseData);
    }

    /**
     * Delete image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if (!is_numeric($request->id)) {
            return $this->forbiddenResponse([], __('Invalid Request!'));
        }
        return $this->imageService->delete($request->id) ? $this->okResponse([], __('Image Deleted Successfully')) : $this->notFoundResponse([], );
    }

    /**
     * View image
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function view($id)
    {
        if (!is_numeric($id)) {
            return $this->forbiddenResponse([], __('Invalid Request!'));
        }
        $image = $this->imageService->details($id);

        if ($image) {
            return $this->okResponse(new ImageResource($image));
        }

        return $this->notFoundResponse([], );
    }
}


