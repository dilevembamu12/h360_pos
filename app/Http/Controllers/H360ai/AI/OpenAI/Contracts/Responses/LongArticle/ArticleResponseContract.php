<?php

namespace Modules\OpenAI\Contracts\Responses\LongArticle;

interface ArticleResponseContract extends LongArticleResponseContract
{
    public function content(): string;
}