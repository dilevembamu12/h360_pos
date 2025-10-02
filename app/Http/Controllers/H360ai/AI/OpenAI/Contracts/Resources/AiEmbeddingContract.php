<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\AiEmbedding\AiEmbeddingResponseContract;

interface AiEmbeddingContract
{
    /**
     * Provide the provider options for Ai Embedding settings.
     *
     * @return array
     */
    public function aiEmbeddingOptions(): array;

   /**
    * Create embeddings using the provided AI options.
    *
    * @param array $aiOptions Options for AI configuration.
    * @return AiEmbeddingResponseContract The resulting embeddings.
    */
   public function createEmbeddings(array $aiOptions): AiEmbeddingResponseContract;
}
