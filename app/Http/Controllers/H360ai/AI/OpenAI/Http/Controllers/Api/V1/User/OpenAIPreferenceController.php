<?php

namespace Modules\OpenAI\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Modules\OpenAI\Services\{
    ContentService,
    CodeService,
    ChatService,
    ImageService
};
use Modules\OpenAI\Transformers\Api\V1\{
    PreferenceResource,
    ChatResource
};
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\OpenAI\Http\Resources\ConversationResource;
use Illuminate\Pagination\Paginator;

class OpenAIPreferenceController extends Controller
{
    /**
    * Content Preferences
    * @param ContentService $contentService
    * @return [type]
    */
    public function contentPreferences(ContentService $contentService)
    {
        $document = $contentService->getMeta('document');
        return $this->successResponse(new PreferenceResource($document));
    }

    /**
    * Image Maker Preferences
    * @param ContentService $contentService
    * @param ImageService $imageService
    * @return [type]
    */
    public function imagePreferences(ContentService $contentService, ImageService $imageService)
    {
        $imageMakerObject = $contentService->getMeta('image_maker');

        $providers = $imageService->filterImageProviders($imageMakerObject->imageCreateFrom);

        $imageCreateForm = [];

        foreach($providers as $value) {
            $providerName = ucfirst(str_replace('_', '', $value));
            $imageCreateForm[$providerName] = $providerName;
        }

        $response = [
            'image_createFrom' => $imageCreateForm
        ];

        foreach ($imageCreateForm as $data) {
            $response[$data] = [];
        }
        
        foreach ($imageMakerObject->metaData as $meta) {
            $parts = explode("_", $meta->key);
            $lastValue = end($parts);
        
            foreach ($providers as $provider) {
                if (str_contains($meta->key, $provider)) {
                    $providerName = ucfirst(str_replace('_', '', $provider));
                    $response[$providerName][$lastValue] = json_decode($meta->value, true);
        
                    if (in_array($provider, ['openai', 'stable_diffusion'])) {
                        $configKey = $provider == 'openai' ? 'openAIImageModel' : 'stableDiffusion';
                        $response[$providerName]['model'] = array_keys(config('openAI.' . $configKey));
                    }
                }
            }
        }
        
        return $this->successResponse($response);
    }

    /**
    * Code Writer Preferences
    * @param CodeService $codeService
    * @return [type]
    */
    public function codePreferences(CodeService $codeService)
    {
        $codeWriter = $codeService->getMeta('code_writer');
        return $this->successResponse(new PreferenceResource($codeWriter));
    }

    /**
    * Chat Preferences
    * @return [type]
    */
    public function chatPreferences(ChatService $chatService)
    {
        $chatBot = $chatService->getBotName();
        return $this->successResponse(new ChatResource($chatBot));
    }

    /**
     * Providers with his model
     * @return [type]
     */
    public function aiProviders(ContentService $contentService)
    {
        return $this->successResponse($contentService->aiProviders());
    }

    /**
     * all AI data
     * @param ContentService $contentService
     * @param ImageService $imageService
     * 
     * @return [type]
     */
    public function conversationData(ChatService $chatService, ImageService $imageService)
    {
        $configs        = $this->initialize([], request()->all());
        $data = $chatService->conversationData($imageService);
        
        if (sizeof($data) < 0) {
            return $this->okResponse([], __('No data found'));
        }
        $perPage = (int) $configs['rows_per_page']; 
        $page = (int) request()->get('page', 1); 
        
        $collection = new Collection($data);

        // Paginate the collection manually as data comes as arary foramt. We will refactor it later.
        $paginatedData = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );

        $responseData = ConversationResource::collection($paginatedData)->response()->getData(true);
        return $this->response($responseData);
    }
}
