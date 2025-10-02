<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\AiChat;

use Modules\OpenAI\Contracts\Responses\AiChat\AiChatResponseContract;
use Exception;

class AiChatResponse implements AiChatResponseContract
{
    public $content;
    public $response;
    public $expense;
    public $word;

    public function __construct($aiResponse)
    {
        $this->response = $aiResponse;
        $this->content();
        $this->words();
        $this->expense();
    }

    /**
     * Get the content of the response.
     *
     * @return string
     */
    public function content(): string
    {
        $this->content = $this->response->choices[0]->message->content;
        return $this->content;
    }

    /**
     * Get the word count of the response.
     *
     * @return int The number of words in the response.
     */
    public function words(): int
    {
        return $this->word = preference('word_count_method') == 'token'
                ? (int) subscription('tokenToWord', $this->response->usage->completionTokens)
                : countWords($this->response->choices[0]->message->content);
    }

    /**
     * Get the expense associated with generating the response.
     *
     * @return int The expense in some currency (e.g., dollars).
     */
    public function expense(): int
    {
        return $this->expense = $this->response->usage->totalTokens;
    }

    /**
     * Get the response content.
     *
     * @return mixed The content of the response.
     */
    public function response(): mixed
    {
        return $this->response;
    }

    /**
     * Handles exceptions by creating and returning an Exception instance.
     *
     * @param string $message The exception message.
     * @return \Exception The created exception instance.
     */
    public function handleException(string $message): Exception
    {
        throw new Exception($message);
    }
}