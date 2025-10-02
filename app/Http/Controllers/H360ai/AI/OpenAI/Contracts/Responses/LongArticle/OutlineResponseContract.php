<?php

namespace Modules\OpenAI\Contracts\Responses\LongArticle;

use Modules\OpenAI\Contracts\Responses\LongArticle\LongArticleResponseContract;

interface OutlineResponseContract extends LongArticleResponseContract
{
    /**
     * Get the generated outlines.
     *
     * @return array The generated outlines.
     *
     * @example [
     *     [
     *          "Introduction: What is a Long Article?",
     *          "Body: Discussing the benefits of long articles",
     *          "Conclusion: Summary of the key points"
     *     ],
     *     [
     *          "Introduction: What is a Long Article?",
     *          "Body: Discussing the benefits of long articles",
     *          "Conclusion: Summary of the key points"
     *     ],
     * ]
     */
    public function content(): array;
}
