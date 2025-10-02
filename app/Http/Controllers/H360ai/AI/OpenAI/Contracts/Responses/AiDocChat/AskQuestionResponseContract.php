<?php

namespace Modules\OpenAI\Contracts\Responses\AiDocChat;

interface AskQuestionResponseContract
{
    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed;

    /**
     * Get the content.
     *
     * @return string The content of the response as an string.
     */
    public function content(): string;

    /**
     * Get the word count of the response.
     *
     * @return int The number of words in the response.
     */
    public function words(): int;

    /**
     * Get the expense associated with generating the response.
     *
     * @return int The expense in some currency (e.g., tokens).
     */
    public function expense(): int;

    /**
     * Handle any errors that occurred during the response generation.
     *
     */
    public function handleException(string $message): \Exception;
}