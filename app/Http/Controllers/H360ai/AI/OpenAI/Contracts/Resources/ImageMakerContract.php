<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\ImageResponseContract;

interface ImageMakerContract
{
    /**
     * Provide the provider options for long article settings.
     *
     * @return array
     */
    public function imageMakerOptions(): array;

    public function imageMakerRules(): array;

    public function generateImage(array $aiOptions): ImageResponseContract;
}