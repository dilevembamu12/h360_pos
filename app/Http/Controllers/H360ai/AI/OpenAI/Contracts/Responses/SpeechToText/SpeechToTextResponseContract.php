<?php

namespace Modules\OpenAI\Contracts\Responses\SpeechToText;
use Exception;

interface SpeechToTextResponseContract
{
    public function duration(): string;

    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed;

    /**
     * Get the word count of the response.
     *
     * @return int The number of words in the response.
     */
    public function words(): int;

    /**
     * Handle any errors that occurred during the response generation.
     *
     * @throws Exception If an error occurred during response generation.
     */
    public function handleException(string $message): Exception;
}