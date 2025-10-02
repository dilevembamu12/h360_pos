<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\AiDetector\AiDetectorGenerateResponseContract;

interface AiDetectorContract
{
    /**
     * Provide the provider options for template settings.
     *
     * @return array
     */
    public function aiDetectorOptions(): array;
    
    /**
     * Detects AI-generated content based on the provided AI options.
     *
     * @param array $aiOptions An array of options for AI content detection.
     * 
     * @return \App\Contracts\AiDetectorGenerateResponseContract
     */
    public function aiDetector(array $aiOptions): AiDetectorGenerateResponseContract;
    
}
