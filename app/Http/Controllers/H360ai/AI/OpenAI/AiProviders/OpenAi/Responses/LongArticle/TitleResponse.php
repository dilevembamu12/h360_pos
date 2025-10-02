<?php 

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\LongArticle;

use Modules\OpenAI\Contracts\Responses\LongArticle\TitleResponseContract;
use Exception;

class TitleResponse implements TitleResponseContract
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

    public function content(): array
    {
        $content = $this->response->choices[0]->message->content;

        if (is_string($content)) {
            $content = json_decode($content);

            if (is_null($content)) {
                $this->handleException(__('Something went wrong with title generation. Please try again.'));
            }

            if (! json_last_error() === JSON_ERROR_NONE) {
                $this->handleException(__('Something went wrong with title generation'));
            } 
        }

        return $this->content = $content;
    }

    public function words(): int
    {
        return $this->word = preference('word_count_method') == 'token' 
                            ? (int) subscription('tokenToWord', $this->response->usage->completionTokens) 
                            : countWords($this->response->choices[0]->message->content);

    }

    public function expense(): int
    {
        return $this->expense = $this->response->usage->totalTokens;
    }

    public function response(): mixed
    {
        return $this->response;
    }

    public function handleException(string $message): Exception
    {
        throw new \Exception($message);
    }
}