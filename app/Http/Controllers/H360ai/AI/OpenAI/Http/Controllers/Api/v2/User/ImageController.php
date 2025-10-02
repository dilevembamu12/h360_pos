<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Exception, DB;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Http\Requests\v2\ImageStoreRequest;
use Modules\OpenAI\Services\v2\ImageService;
use Modules\OpenAI\Transformers\Api\v2\Image\{
    SingleImageResources,
    ImageReplyResources
};

use Modules\OpenAI\Http\Requests\ToggleFavoriteImageRequest;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * @var $imageService The instance of the chat service.
     */
    protected $imageService;

    public function __construct()
    {
        $this->imageService = new ImageService();
    }

    public function index()
    {
        $images = Archive::with('metas', 'imageCreator', 'imageCreator.metas') // Eager load relationships
            ->leftJoin('archives_meta as meta_creator', function ($join) {
                $join->on('archives.id', '=', 'meta_creator.owner_id')
                    ->where('meta_creator.key', '=', 'image_creator_id')
                    ->where('meta_creator.value', auth()->id());
            })
            ->leftJoin('archives_meta as meta_size', function ($join) {
                $join->on('archives.id', '=', 'meta_size.owner_id')
                    ->where('meta_size.key', '=', 'generation_options');
            })
            ->leftJoin('users as creators', 'meta_creator.value', '=', 'creators.id')
            ->where(function ($query) {
                $query->where('meta_creator.value', auth()->id());
            })
            ->select([
                'archives.*',
                'creators.name as creator_name',
            ])
            ->where('archives.type', 'image_variant')
            ->filter('Modules\\OpenAI\\Filters\\v2\\ImageFilter')
            ->paginate(preference('row_per_page'));


        return SingleImageResources::collection($images);
    }

    /**
     * Store a newly created image in the storage.
     *
     * @param  \App\Http\Requests\ImageStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception if storing the image fails
     */
    public function store(ImageStoreRequest $request)
    {
        $validatedRequest = $request->validated();
        $optionRequest['options'] = $request->except(['_token', 'prompt', 'provider']);
        $request = array_merge($validatedRequest, $optionRequest);
        $request['options']['file'] = request('file') ?? null;
        $cleanedString = preg_replace('/[^A-Za-z0-9\s]/', '', $request['prompt']);
        $request['prompt'] = filteringBadWords($cleanedString);
        $request['parent_id'] = Archive::where(['id' => request('parent_id'), 'type' => 'image'])->first() ? request('parent_id')  : null;

        try {
            $images = new ImageReplyResources($this->imageService->store($request));
            return response()->json(['data' => $images], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified image from storage.
     *
     * @param  int  $imageId  The ID of the image to be deleted.
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception if deletion fails.
     */
    public function destroy($imageId)
    {
        if (! is_numeric($imageId)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        DB::beginTransaction();

        try {

            $this->imageService->delete(['id' => $imageId]);
            DB::commit();
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('Image')])] , Response::HTTP_OK);

        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            
        }
    }

    /**
     * Toggle favorite image
     *
     * @param ToggleFavoriteImageRequest $request
     *
     * @return mixed
     */
    public function toggleFavorite(ToggleFavoriteImageRequest $request): mixed
    {
        $authUser = auth()->user();
        $favoritesArray = $authUser->image_favorites ?? [];
        
        $imageId = $request->image_id;
        $toggleState = $request->toggle_state;

        $image = Archive::where('id', $imageId)->whereType('image_variant')->first();

        if (! $image) {
            return response()->json(['message' => __("No data found")], Response::HTTP_NOT_FOUND);
        }
        
        if ($toggleState === 'false' && !in_array($imageId, $favoritesArray)) {
            return response()->json(['message' => __("Invalid Request")], Response::HTTP_FORBIDDEN);
        }
        
        try {
            if ($toggleState === 'true') {
                $favoritesArray = array_unique([...$favoritesArray, $imageId]);
                $message = __("Successfully marked favorite!");
            } else {
                $favoritesArray = array_diff($favoritesArray, [$imageId]);
                $message = __("Successfully removed from favorites!");
            }
        
            $authUser->image_favorites = $favoritesArray;
            $authUser->save();
        } catch (Exception $e) {
            return response()->json(['error' => __("Failed to update favorites! Please try again later.")], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return response()->json(['message' => $message], Response::HTTP_OK);
        
    }
}
