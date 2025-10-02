<?php

namespace Modules\OpenAI\Contracts\Responses\LongArticle;

use Modules\OpenAI\Contracts\Responses\LongArticle\LongArticleResponseContract;

interface TitleResponseContract extends LongArticleResponseContract
{
    /**
     * Get the generated titles.
     *
     * @return array The generated titles.
     * 
     * @example ["Title 1", "Title 2", "Title 3"]
     */
    public function content(): array;
}