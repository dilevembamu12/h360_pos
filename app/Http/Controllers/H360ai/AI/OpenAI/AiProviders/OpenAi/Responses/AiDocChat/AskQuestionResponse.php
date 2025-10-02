<?php 

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\AiDocChat;

use Modules\OpenAI\Contracts\Responses\AiDocChat\AskQuestionResponseContract;
use Exception;

class AskQuestionResponse implements AskQuestionResponseContract
{
    public $content;
    public $response;
    public $expense;
    public $words;

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

    public function expense(): int
    {
         return $this->expense = $this->response->usage->totalTokens;
    }

    public function words(): int
    {
        return $this->words = countWords($this->response->choices[0]->message->content);
    }

    public function response(): mixed
    {
        return $this->response;
    }

    public function handleException(string $message): Exception
    {
        throw new Exception($message);
    }
}