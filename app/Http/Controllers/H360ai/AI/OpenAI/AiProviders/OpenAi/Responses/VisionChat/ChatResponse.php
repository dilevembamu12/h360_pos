<?php 

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\VisionChat;

use Modules\OpenAI\Contracts\Responses\VisionChat\VisionChatResponseContract;
use Exception;

class ChatResponse implements VisionChatResponseContract
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

    public function content(): string
    {
        return $this->content = $this->response->choices[0]->message->content;
    }

    // NOTE:: Word count will be depend on provider word count method - need refactor after complete
    public function words(): int
    {
        return $this->word = str_word_count($this->response->choices[0]->message->content);
    }

    // NOTE:: Expense count will be modified according to common expense calculation
    public function expense(): object
    {
        return $this->expense = $this->response->usage;
    }

    public function response(): mixed
    {
        return $this->response = $this->response;
    }

    public function handleException(string $message): Exception
    {
        throw new \Exception($message);
    }
}