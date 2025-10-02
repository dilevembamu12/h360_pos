<?php

namespace Modules\OpenAI\AiProviders\Clipdrop;

use App\Lib\AiProvider;
use Modules\OpenAI\AiProviders\Clipdrop\Resources\ImageDataProcessor;
use Modules\OpenAI\AiProviders\Clipdrop\Responses\ImageResponse;
use Modules\OpenAI\AiProviders\Clipdrop\Traits\ClipdropApiTrait;
use Modules\OpenAI\Contracts\Resources\ImageMakerContract;
use Modules\OpenAI\Contracts\Responses\ImageResponseContract;

class ClipdropProvider extends AiProvider implements ImageMakerContract
{
    use ClipdropApiTrait;

    protected $processedData;

    /**
     * Return the options for the image maker.
     *
     * @return array Options for the image maker.
     */
    public function imageMakerOptions(): array
    {
        return (new ImageDataProcessor())->imageOptions();
    }


    /**
     * Retrieve the description for the current AI provider.
     *
     * @return array A description of the AI provider.
     */
    public function description(): array
    {
        return [
            'title' => 'Clipdrop',
            'description' => __('Clipdrop provides a suite of AI-powered image editing APIs that allow users to perform various tasks such as removing backgrounds, inpainting, upscaling, and more.'),
            'image' => 'Modules/OpenAI/Resources/assets/image/clipdrop.png',
        ];
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
     * Retrieve the validation rules for the current data processor.
     * 
     * @return array An array of validation rules.
     */
    public function imageMakerRules(): array
    {
        return (new ImageDataProcessor)->rules();
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
        $processorClass = "Modules\\OpenAI\\AiProviders\\Clipdrop\\Resources" . $processor;

        if (class_exists($processorClass)) {
            return (new $processorClass())->validationRules();
        }

        return [];
    }
}
