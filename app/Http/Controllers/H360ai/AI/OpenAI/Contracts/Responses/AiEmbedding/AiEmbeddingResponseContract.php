<?php

namespace Modules\OpenAI\Contracts\Responses\AiEmbedding;

interface AiEmbeddingResponseContract
{
    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed;

    /**
     * Get the content of the resource.
     *
     * @return string The content of the resource.
     */
    public function content(): array;

    /**
     * Get the expense associated with generating the response.
     *
     * @return int The expense in some currency (e.g., dollars).
     */
    public function expense(): int;

    /**
     * Handle any errors that occurred during the response generation.
     *
     * @throws \Exception If an error occurred during response generation.
     */
    public function handleException(string $message): \Exception;
}