<?php

namespace Modules\OpenAI\Contracts\Responses;

interface ResponseContract
{
    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed;

    /**
     * Get the expense associated with generating the response.
     *
     * @return int The expense in some currency (e.g., dollars).
     */
    public function expense(): int;

    /**
     * Get the word count of the response.
     *
     * @return int The number of words in the response.
     */
    public function words(): int;

    /**
     * Handle any errors that occurred during the response generation.
     *
     * @throws ResponseGenerationException If an error occurred during response generation.
     */
    public function handleException(string $message): \Exception;
}
