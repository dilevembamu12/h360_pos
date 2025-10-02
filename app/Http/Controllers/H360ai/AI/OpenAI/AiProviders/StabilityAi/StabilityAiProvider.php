<?php

namespace Modules\OpenAI\AiProviders\StabilityAi;

use App\Lib\AiProvider;
use Modules\OpenAI\AiProviders\StabilityAi\Resources\ImageDataProcessor;
use Modules\OpenAI\AiProviders\StabilityAi\Responses\ImageResponse;
use Modules\OpenAI\AiProviders\StabilityAi\Traits\StabilityAiApiTrait;
use Modules\OpenAI\Contracts\Resources\ImageMakerContract;
use Modules\OpenAI\Contracts\Responses\ImageResponseContract;
use Modules\OpenAI\AiProviders\StabilityAi\Resources\VideoDataProcessor;
use Modules\OpenAI\AiProviders\StabilityAi\Responses\Video\VideoResponseContract;
use Modules\OpenAI\Contracts\Resources\VideoMakerContract;
use Modules\OpenAI\AiProviders\StabilityAi\Responses\Video\VideoResponse;

class StabilityAiProvider extends AiProvider implements ImageMakerContract, VideoMakerContract
{
    use StabilityAiApiTrait;

    protected $processedData;

    /**
     * Return the description of the AI provider.
     *
     * @return array An array containing the title, description, and image of the AI provider.
     */
    public function description(): array
    {
        return [
            'title' => 'Stability AI',
            'description' => __('Stability AI provides a suite of generative AI models, including Text to image and Image to image. These models are designed to be accessible and easy to use, with a focus on quality and realism. Stability AI also offers a platform for developers to build and deploy their own generative AI applications.'),
            'image' => 'Modules/OpenAI/Resources/assets/image/stability.png',
        ];
    }

    public function videoMakerOptions(): array
    {
        return (new VideoDataProcessor())->videoOptions();
    }

     /**
     * Generates a CodeResponseContract object by processing the given $aiOptions using the CodeDataProcessor class.
     *
     * @param array $aiOptions The options for AI processing.
     * @return CodeResponseContract The generated CodeResponseContract object.
     */
    public function generateVideo(array $aiOptions): VideoResponseContract
    {
        $this->processedData = (new VideoDataProcessor($aiOptions))->generateVideo();
        return new VideoResponse($this->makeCurlRequest(moduleConfig('openai.image_to_video_url'), "POST", $this->processedData));
    }

    public function getVideo($id)
    {
        $result = $this->makeCurlRequest(moduleConfig('openai.fetch_video_url'). $id, "GET");
        
        while (isset($result['body']['status']) && $result['body']['status'] == 'in-progress') {
            $result = $this->makeCurlRequest(moduleConfig('openai.fetch_video_url'). $id, "GET");
        }

        return $result;
    }

    /**video
     * Return the options for the image maker.
     *
     * @return array Options for the image maker.
     */
    public function imageMakerOptions(): array
    {
        return (new ImageDataProcessor())->imageOptions();
    }

    /**
     * Retrieve the validation rules for the current data processor.
     *
     * @return array An array of validation rules.
     */
    public function imageMakerRules(): array
    {
        return (new ImageDataProcessor)->rules();
    }

    /**
     * Generate an image using AI options.
     *
     * @param array $aiOptions An associative array of AI options to be used for image generation.
     * @return ImageResponseContract The generated image response.
     */
    public function generateImage(array $aiOptions): ImageResponseContract
    {
        $this->processedData = (new ImageDataProcessor($aiOptions))->imageData();
        return new ImageResponse($this->images());
    }

    /**
     * Get the validation rules for a specific processor.
     * 
     * @param string $processor The name of the data processor class.
     * 
     * @return array Validation rules for the processor.
     */
    public function getValidationRules(string $processor): array
    {
        $processorClass = "Modules\\OpenAI\\AiProviders\\StabilityAi\\Resources" . $processor;

        if (class_exists($processorClass)) {
            return (new $processorClass())->validationRules();
        }

        return [];
    }

    /**
     * Retrieve the validation rules for the current data processor.
     * 
     * @param string $processor The name of the data processor class.
     * 
     * @return array An array of validation rules.
     */
    public function videoValidationRules(string $processor): array
    {
        $processorClass = "Modules\\OpenAI\\AiProviders\\StabilityAi\\Resources\\" . $processor;

        if (class_exists($processorClass)) {
            return (new $processorClass())->videoValidationRules();
        }

        return [];
    }
}
