<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\Plagiarism\GenerateResponseContract;

interface PlagiarismContract
{
    /**
     * Provide the provider options for template settings.
     *
     * @return array
     */
    public function plagiarismOptions(): array;

    public function plagiarism(array $aiOptions): GenerateResponseContract;
    
}
