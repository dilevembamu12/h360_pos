<?php

namespace Modules\OpenAI\Contracts\Responses\Plagiarism;

interface ReportResponseContract extends PlagiarismResponseContract
{
    /**
     * Get the generated response.
     *
     * @return string The generated response.
     */
    public function report(): array;
}
