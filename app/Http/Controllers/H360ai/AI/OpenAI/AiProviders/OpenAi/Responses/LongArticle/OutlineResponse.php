<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\LongArticle;

use Modules\OpenAI\Contracts\Responses\LongArticle\OutlineResponseContract;
use Exception;

class OutlineResponse implements OutlineResponseContract
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
        $content = [];
        if (count($this->response->choices) > 1) {
            foreach ($this->response->choices as $choice) {
                $content[] = is_array($choice->message->content) ? $choice->message->content : json_decode($choice->message->content) ; 
            }
        } else {
            $content[] = is_array($this->response->choices[0]->message->content) ? $this->response->choices[0]->message->content : json_decode($this->response->choices[0]->message->content) ;
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