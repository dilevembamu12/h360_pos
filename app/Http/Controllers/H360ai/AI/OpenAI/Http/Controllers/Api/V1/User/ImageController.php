<?php
/**
 * @package ImageController
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 10-04-2023
 */
namespace Modules\OpenAI\Http\Controllers\Api\V1\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ModelTraits\Filterable;
use Modules\OpenAI\Http\Controllers\Api\v2\User\ChatController;
use Modules\OpenAI\Services\ImageService;
use Modules\OpenAI\Http\Resources\ImageResource;
use Modules\OpenAI\Services\ChatService;
use Modules\OpenAI\Http\Requests\ConversationRequest;

class ImageController extends Controller
{
    /**
     * Use Filtable trait.
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
        $images = $this->imageService->model()->select('*', 'meta->created-at as createdTime')->where(['user_id' => auth('api')->user()->id])->orderBy("id", "desc")->groupBy('createdTime');
        if (count(request()->query()) > 0) {
            $images = $images->filter();
        }

        $contents = $images->with(['User:id,name'])->paginate($configs['rows_per_page']);
        $responseData = ImageResource::collection($contents)->response()->getData(true);
        return $this->response($responseData);
    }

    /**
     * Image conversation list.
     * @return [type]
     */
    public function converstaionList()
    {
        $configs        = $this->initialize([], request()->all());
        $images = $this->imageService->model()->whereNull('parent_id')->orWhereNotNull('parent_id')->where('user_id', auth('api')->user()->id)->orderBy("id", "desc")->groupBy('parent_id');
        if (count(request()->query()) > 0) {
            $images = $images->filter();
        }

        $contents = $images->with(['User:id,name'])->paginate($configs['rows_per_page']);
        $responseData = ImageResource::collection($contents)->response()->getData(true);
        return $this->response($responseData);
    }

    /**
     * Conversation Image
     * @param mixed $id
     * 
     * @return [type]
     */
    public function converstaionView($id)
    {
        if (!is_numeric($id)) {
            return $this->forbiddenResponse([], __('Invalid Request!'));
        }

        $configs        = $this->initialize([], request()->all());

        $userRole = auth()->user()->roles()->first();

        $images = $this->imageService->model()->select('*', 'meta->created-at as createdTime')->where(['id' => $id])->whereNull('parent_id');

        if ($userRole->type == 'user') {
            $images = $images->where(['user_id' => auth('api')->user()->id]);
        }

        $images = $images->orWhere(['parent_id' => $id])->orderBy("id", "desc")->groupBy('createdTime');
        
        if (count(request()->query()) > 0) {
            $images = $images->filter();
        }

        $contents = $images->with(['User:id,name'])->paginate($configs['rows_per_page']);

        if ($contents->total() > 0) {
            $responseData = ImageResource::collection($contents)->response()->getData(true);
            return $this->response($responseData);
        }
        return $this->notFoundResponse([], __('No :x found.', ['x' => __('Image')]));
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

        return $this->notFoundResponse([], __('No :x found.', ['x' => __('Image')]));
    }

    public function conversationDelete(ConversationRequest $request)
    {
        $data = ['user_reply', 'file_id', 'bot_reply', 'total_words', 'total_token', 'prompt_tokens', 'completion_tokens'];
        $delete = match ($request->type) {
            'Image' => $this->imageService->delete(request('id')),
            'file', 'url' => (new ChatService())->destroy(request('id'), request('type'), $data),
            'chat' => (new ChatController)->destroy(request('id'))
        };
  
        if ($delete && $request->type != 'Image') {
            $content = $delete->getContent();
            $data = json_decode($content, true);
        }
        
    
        // Check if the response contains an error
        if (isset($data['error']) || !$delete) {
            return $this->notFoundResponse([], __('No :x found.', ['x' => __(request('type'))]));
        } else {
            return $this->okResponse([], __(':x Deleted Successfully', ['x' => __(request('type'))]));
        }
    }
}


