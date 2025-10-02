<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\VisionChat\{
    VisionChatResponseContract
};

interface VisionChatContract
{
    /**
     * Provide the provider options for Vision Chat settings.
     *
     * @return array
     */
    public function visionChatOptions(): array;

    /**
     * Generate image details based on provided image.
     *
     * @param array $aiOptions
     */
    public function visionChat(array $aiOptions): VisionChatResponseContract;
    
}
