<?php

namespace Modules\OpenAI\Contracts\Responses\Plagiarism;

interface PlagiarismResponseContract
{
    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed;

    /**
     * Handle any errors that occurred during the response generation.
     *
     * @throws \Exception If an error occurred during response generation.
     */
    public function handleException(string $message): \Exception;
}
