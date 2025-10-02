<?php

namespace Modules\OpenAI\Contracts\Responses\AiDetector;

interface AiDetectorReportResponseContract extends AiDetectorResponseContract
{
    /**
     * Get the generated response.
     *
     * @return string The generated response.
     */
    public function report(): array;
}
