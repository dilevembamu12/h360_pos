<?php

namespace Modules\OpenAI\Contracts\Responses\AiChat;

interface AiChatResponseContract
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
     * Get the content of the response.
     *
     * @return string
     */
    public function content(): string;

    /**
     * Handles exceptions by creating and returning an Exception instance.
     *
     * @param string $message The exception message.
     * @return \Exception The created exception instance.
     */
    public function handleException(string $message): \Exception;
}