<?php

namespace Modules\OpenAI\Services\v2;

use Storage, Exception, Str, DB, AiProviderManager;
use Illuminate\Database\Eloquent\Collection;
use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Entities\Archive;
use Illuminate\Http\Response;
use Modules\OpenAI\Services\v2\ArchiveService;
use App\Models\{
    Team,
    TeamMemberMeta
};


class ImageService
{
    private $aiProvider;
    private $production = true;
    
    /**
     * Method __construct
     *
     * @param ImageGenerator $generator [decide which AI provider will be used for generate]
     *
     * @return void
     */
    public function __construct() 
    {
        if(! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'imagemaker');
            if (! $this->aiProvider) {
                throw new Exception(__('Provider is not available for generate image. Please contact with system administrator'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    public function store(array $requestData)
    {  
        // Check User Subscription Availability
        $this->checkUserImageSubscription($requestData);

        // Request to AI provider for image generation
        if ($this->production) {
            $response = $this->aiProvider->generateImage($requestData);
        } else {
            $response = $this->aiProvider->fakeGenerateImage($requestData);
        }
 
        // Checked Image is return in correct format
        $images = $response->images();
        foreach ($images as $image) {
            if (! ($image instanceof \Intervention\Image\Image)) {
                throw new Exception(__('Invalid image type.'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        
        // Saving image to Disk
        $imageUrls = $this->saveImages($images);

        // Storing in Database
        DB::beginTransaction();

        try {

            $image = '';
            if (! $requestData['parent_id']) {
                $image = ArchiveService::create([
                    'user_id' => auth()->id(),
                    'title' => $requestData['prompt'],
                    'unique_identifier' => (string) Str::uuid(),
                    'type' => 'image',
                    'images' => $imageUrls,
                    'generation_options' => $requestData['options']
                ]);
            }

            $userReply = ArchiveService::create([
                'parent_id' => $requestData['parent_id'] ?? $image->id,
                'user_id' => auth()->id(),
                'title' => $requestData['prompt'],
                'unique_identifier' => (string) Str::uuid(),
                'type' => 'image_chat',
                'user_reply' => $requestData['prompt']
            ]);
            
            $imageReply = ArchiveService::create([
                'parent_id' => $requestData['parent_id'] ?? $image->id,
                'title' => $requestData['prompt'],
                'unique_identifier' => (string) Str::uuid(),
                'provider' => $requestData['provider'],
                'type' => 'image_chat',
                'images_urls' => $imageUrls,
                'generation_options' => $requestData['options'],
            ]);

            foreach ($imageUrls as $url) {

                $imageVariant = ArchiveService::create([
                    'parent_id' => $imageReply->id,
                    'unique_identifier' => (string) Str::uuid(),
                    'provider' => $requestData['provider'],
                    'type' => 'image_variant',
                    'title' => $requestData['prompt'],
                    'url' => $url,
                    'original_name' => basename(str_replace('\\', '/', $url)),
                    'slug' => $this->slug($requestData['prompt']),
                    'image_creator_id' => auth()->id(),
                    'generation_options' => $requestData['options'],
                ]);
            }
   
            // Update User balance
            $balanceReduceType = $this->updateUserBalance($requestData);

            // Return new Image Reply
            $reply = ArchiveService::show($imageReply->id); 
            if ($balanceReduceType) {
                $reply->balance_reduce_type = $balanceReduceType;
            }

            DB::commit();

            return $reply;
           
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function delete($requestData)
    {
        if (isset($requestData['type']) && $requestData['type'] === 'video') {
            return $this->deleteVideo($requestData);
        }

        try {
            ArchiveService::delete(
                (int) $requestData['id'], 
                'image_variant', 
                null, 
                ['key' => 'image_creator_id', 'value' => auth()->id()]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Deletes a video variant from the database.
     * 
     * @param array $requestData The request data containing the 'id' of the video variant to be deleted.
     * 
     * @throws \Exception If the deletion fails.
     * 
     * @return void
     */

    public function deleteVideo($requestData)
    {
        try {
                ArchiveService::delete(
                    (int) $requestData['id'], 
                    'video', 
                    null, 
                    ['key' => 'video_creator_id', 'value' => auth()->id()]
                );
            } catch (Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
            }
    }

    /**
     * Toggle image as favorite for the authenticated user.
     *
     * @param array $requestData The request data containing 'toggle_state' and 'image_id'.
     * @return string Success message indicating the toggle action.
     * @throws \Exception If required keys are missing or saving user data fails.
     */
    public function favourite($requestData): string
    {
        $user = auth()->user();
        $favouriteImages = $user->image_favorites ?? [];

        if ($requestData['toggle_state'] == 'true') {
            $favouriteImages = array_unique(array_merge($favouriteImages, [$requestData['image_id']]), SORT_NUMERIC);
            $message = __("Successfully marked favorite!");
        } else {
            $favouriteImages = array_diff($favouriteImages, [$requestData['image_id']]);
            $message = __("Successfully removed from favorites!");
        }

        $user->image_favorites = $favouriteImages;
        $user->save();

        return $message;
    }

    /**
     * Save uploaded images and their thumbnails.
     *
     * @param array $images Array of uploaded images.
     * @return array Array of URLs for the saved images.
     * @throws Exception If there are errors during image processing or storage.
     */
    public function saveImages(array $images): array
    {
        $imageUrls = [];
        foreach ($images as $image) {

            $fileExtension = str_replace('image/', '', $image->mime);
            $fileName = md5(uniqid()) . "." . $fileExtension;

            // Save Main Image
            Storage::disk()->put(
                createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiImages'])) . 
                DIRECTORY_SEPARATOR . 
                $fileName, 
                $image->encode()
            );

             // Resize and save thumbnails
            foreach (['small' => 150, 'medium' => 512] as $key => $ratio) {
                try {
                    // Resizing Image
                    $thumbnailImage = clone $image;
                    $thumbnailImage->resize($image->height(), $ratio, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    // Save as thumbnail
                    Storage::disk()->put(
                        createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads', 'sizes', $key])) . DIRECTORY_SEPARATOR .  
                        $fileName, 
                        $thumbnailImage->encode()
                    );
                 
                } catch (\Intervention\Image\Exception\NotReadableException $e) {
                    throw new Exception($e->getMessage());
                }
            }

            // Store URL for main image
            $imageUrls[] = join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiImages']) . 
            DIRECTORY_SEPARATOR . 
            $fileName;
        }

        return $imageUrls;
    }

    /**
     * Check if the user has a valid subscription for image services.
     *
     * @param array $requestData The request data containing options.
     * @throws Exception If the user's subscription is invalid or lacks necessary credits.
     * @return void
     */
    public function checkUserImageSubscription(array $requestData): void
    {
        $userId = (new ContentService())->getCurrentMemberUserId('meta', null);

        if (! subscription('isAdminSubscribed')) {

            // User status Actice/Inactive user
            $userStatus = (new ContentService())->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new Exception($userStatus['message']);
            }

            // User Subscribed to a plan or not
            $validation = subscription('isValidSubscription', $userId, 'image');
            if ($validation['status'] == 'fail' && ! auth()->user()->hasCredit('image')) {
                throw new Exception($validation['message']);
            }

            if (!isset($requestData['options']['size'])) {
                throw new Exception(__('Resolution is required for generate image. Please contact with the administration.'));
            }

            // Check Resolution is supported to user Subscribed Plan
            if (
                is_null($requestData['options']['size']) ||
                (filled($requestData['options']['size']) // Exist size data
                && !subscription('isValidResolution', $userId, $requestData['options']['size']) // Check resolution availability in subscription plan
                && !auth()->user()->hasCredit('image')) // Has Image credit
            ) {
                throw new Exception(__('This resolution is not available in your plan.'));
            }
        }
    }

    public function updateUserBalance($requestData)
    {
        $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
        $subscription = subscription('getUserSubscription', $userId);

        if (! subscription('isAdminSubscribed') || auth()->user()->hasCredit('image')) {
            
            $variant = $requestData['options']['variant'] ?? 1;
            $increment = subscription('usageIncrement', $subscription?->id, 'image', $variant, $userId);
            
            if ($increment && $userId != auth()->user()->id) {
                $this->storeTeamMeta($variant);
            }

            return app('user_balance_reduce');
        }
    }

    public function storeTeamMeta($words)
    {
        $memberData = Team::getMember(auth()->user()->id);

        if (!empty($memberData)) {
            $usage = TeamMemberMeta::getMemberMeta($memberData->id, 'image_used');
            if (!empty($usage)) {
                $usage->increment('value', $words); 
            }
        }
    }

    /**
     * Generate a URL-friendly slug based on the given prompt.
     *
     * @param string $prompt The input prompt for generating the slug.
     * @return string The generated slug.
     * @throws \Exception If there's an issue with database querying.
     */
    public function slug($prompt): string
    {
        sleep(1);

        $slug = strlen($prompt) > 120 ? cleanedUrl(substr($prompt, 0, 120)) : cleanedUrl($prompt);

        $slugExist = Archive::query()
            ->select('archives.id')
            ->where('archives.type', 'image_variant')
            ->join('archives_meta', function ($join) use ($slug) {
                $join->on('archives.id', '=', 'archives_meta.owner_id')
                    ->where('archives_meta.key', 'slug')
                    ->where('archives_meta.value', $slug);
            })
            ->exists();

        return $slugExist ? $slug . time() : $slug;
    }

    /**
     * Get image variants related to a given image.
     *
     * @param Archive $image The parent image for which variants are to be retrieved.
     * @return \Illuminate\Database\Eloquent\Collection|Archive[] Collection of image variants.
     */
    public function getImageVariants(Archive $image): Collection
    {
        return Archive::with('metas')
            ->where('parent_id', $image->parent_id)
            ->where('id', '!=', $image->id)
            ->where('type', 'image_variant')
            ->get();
    }

    /**
     * Get related images based on the provided image's title.
     *
     * @param Archive $image The image for which related images are to be retrieved.
     * @return \Illuminate\Database\Eloquent\Collection Collection of related images.
     */
    public function getRelatedImages(Archive $image): Collection
    {
        return Archive::with('metas')
            ->whereLike('title', $image->title)
            ->whereHas('metas', function ($q) {
                $q->where(['key' => 'image_creator_id', 'value' => auth()->id()]);
            })
            ->where('parent_id', '!=', $image->parent_id)
            ->where('type', 'image_variant')
            ->take(4)
            ->get();
    }

    /**
     * Prepares an array of image data with additional metadata.
     *
     * @param \Illuminate\Database\Eloquent\Collection $data
     * @param array $favorites
     * @param string $size
     * @return array
     */
    public function prepareImageData($data, array $favorites = [], string $size): array
    {
        $imageItems = [];
        
        foreach ($data as $image) {
            $imageItems[] = [
                'id' => $image->id,
                'title' => $image->title,
                'slug' => $image->slug,
                'originalImageUrl' => str_replace("\\", "/", $image->imageUrl()),
                'url' => str_replace("\\", "/", $image->imageUrl(['thumbnill' => true, 'size' => $size])),
                'size' => data_get($image->generation_options, 'size', 'None'),
                'is_favorite' => in_array($image->id, $favorites),
                'created_at' => timeZoneFormatDate($image->created_at) . ', ' . timeZoneGetTime($image->created_at),
                'art_style' => data_get($image->generation_options, 'art_style', 'None'),
                'lighting_style' => data_get($image->generation_options, 'light_effect', 'None'),
                'generation_options' => $image->generation_options
            ];
        }

        return $imageItems;
    }

}
