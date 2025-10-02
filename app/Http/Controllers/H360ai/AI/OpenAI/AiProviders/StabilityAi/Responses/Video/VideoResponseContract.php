<?php

namespace Modules\OpenAI\AiProviders\StabilityAi\Responses\Video;

interface VideoResponseContract
{
    public function video();

    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed;

    /**
     * Handle any errors that occurred during the response generation.
     *
     * @throws ResponseGenerationException If an error occurred during response generation.
     */
    public function handleException(string $message): \Exception;
}
