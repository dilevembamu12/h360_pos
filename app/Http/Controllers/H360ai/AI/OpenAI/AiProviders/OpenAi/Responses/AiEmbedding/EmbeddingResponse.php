<?php 

namespace Modules\OpenAI\AiProviders\OpenAi\Responses\AiEmbedding;

use Modules\OpenAI\Contracts\Responses\AiEmbedding\AiEmbeddingResponseContract;
use Exception;
class EmbeddingResponse implements AiEmbeddingResponseContract
{
    public $content;
    public $response;
    public $expense;

    public function __construct($aiResponse)
    {
        $this->response = $aiResponse;
        $this->content();
        $this->expense();
    }

    public function content(): array
    {
        return $this->content = $this->response->embeddings[0]->embedding;
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
        throw new Exception($message);
    }
}