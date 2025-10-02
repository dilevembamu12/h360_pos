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
use Modules\OpenAI\Http\Requests\ToggleFavoriteImageRequest;
use Modules\OpenAI\Services\{
    ImageService,
    ContentService
};


class ImageController extends Controller
{

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
     *
     * @param Request $request
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
    public function list()
    {
        $data['images'] = $this->imageService->getAll()->paginate(preference('row_per_page'));
        $data['userFavoriteImages'] = auth()->user()->image_favorites ?? [];
        return view('openai::blades.images.image_list', $data);
    }

    /**
     * Delete image
     * @param Request $request
     *
     * @return [type]
     */
    public function deleteImage(Request $request)
    {
        $response = ['status' => 'error', 'message' => __('The data you are looking for is not found')];

        if ($this->imageService->delete($request->id)) {
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Image')])];
        }
        return response()->json($response);
    }

    /**
     * View image
     * @param mixed $slug
     *
     * @return [type]
     */
    public function view($slug)
    {
        $data['images'] = $this->imageService->imageBySlug($slug);
        return view('openai::blades.imageView', $data);
    }

    /**
     * All images
     *
     * @param Request $request
     * @return [type]
     */
    public function imageGallery(Request $request)
    {
        $data['currentImage'] = [];
        $data['images'] = $this->imageService->getAll()->paginate(preference('row_per_page'));
        $data['userFavoriteImages'] = auth()->user()->image_favorites ?? [];

        $data['variants'] = [];
        $data['relatedImages'] = [];

        if ($request->ajax()) {
            $imageItems = $this->imageService->prepareImageData($data['images'], $data['userFavoriteImages'],  'medium');

            return response()->json([
                'items' =>  $imageItems,
                'nextPageUrl' => $data['images']->nextPageUrl()
            ]);
        }

        return view('openai::blades.images.image_gallery', $data);
    }

    /**
     * View image
     * @param string $slug
     *
     * @return [type]
     */
    public function imageView($slug)
    {
        $data['currentImage'] = $this->imageService->bySlug($slug);
        $data['userFavoriteImages'] = auth()->user()->image_favorites ?? [];

        $data['variants'] = $this->imageService->variants($data['currentImage']);
        $data['variants']->prepend($data['currentImage']);

        $data['relatedImages'] = $this->imageService->relatedImages($data['currentImage']->name, $data['currentImage']->id);

        $html = view('openai::blades.images.main-image', $data)->render();

        $data['variants'] = $this->imageService->prepareImageData($data['variants'], $data['userFavoriteImages'], 'small');
        $data['relatedImages'] = $this->imageService->prepareImageData($data['relatedImages'], $data['userFavoriteImages'], 'medium');

        return response()->json([
            'data' => $data,
            'html' => $html
        ]);
    }
    
    /**
     * View image
     * @param string $slug
     *
     * @return [type]
     */
    public function imageShare($slug)
    {
        $data['currentImage'] = $this->imageService->bySlug($slug);

        $data['variants'] = $this->imageService->variants($data['currentImage']);
        $data['variants']->prepend($data['currentImage']);

        return view('openai::blades.images.image_view_weblink', $data);
    }

    /**
     * Toggle favorite Image
     */
    public function toggleFavoriteImage(ToggleFavoriteImageRequest $request): mixed
    {
        $authUser = auth()->user();
        $favoritesArray = $authUser->image_favorites ?? [];

        try {
        
            if ($request->toggle_state == 'true') {
                $favoritesArray = array_unique(array_merge($favoritesArray, [$request->image_id]), SORT_NUMERIC);
                $message = __("Successfully marked favorite!");
            } else {
                $favoritesArray = array_diff($favoritesArray, [$request->image_id]);
                $message = __("Successfully removed from favorites!");
            }

            $authUser->image_favorites = $favoritesArray;
            $authUser->save();
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => __("Failed to update favorites! Please try again later.")], 500);
        }

        return response()->json(["success" => true, "message" => $message], 200);
    }

    /**
     * Form field by Image
     * @param Request $request
     * @param ContentService $contentService
     *
     * @return [type]
     */
    public function getFormFiledByImage(Request $request, ContentService $contentService)
    {
        $data['model'] = $request->model;
        $data['option'] = $this->imageService->getModel();
        $userId = $contentService->getCurrentMemberUserId(null, 'session');
        $data['userId'] = $userId; 
        return view('openai::blades.images.image_fields', $data);
    }

    public function imgToVideo()
    {
       return view('openai::blades.img-to-video.imgToVideo');
    }
}


