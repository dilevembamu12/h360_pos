<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\LongArticle\{
    OutlineResponseContract,
    TitleResponseContract
};

interface LongArticleContract
{
    /**
     * Provide the provider options for long article settings.
     *
     * @return array
     */
    public function longArticleOptions(): array;

    /**
     * Generate titles for the long article based on the given AI options.
     *
     * @param array $aiOptions The options for generating titles.
     * @return TitleResponseContract
     */
    public function titles(array $aiOptions): TitleResponseContract;

    /**
     * Generate outlines for the long article based on the given AI options.
     *
     * @param array $aiOptions The options for generating outlines.
     * @return OutlineResponseContract
     */
    public function outlines(array $aiOptions): OutlineResponseContract;

    /**
     * Generate the full article based on the configured options and responses.
     *
     * @param array $aiOptions The options for generating Article.
     */
    public function article(array $aiOptions);
    
    /**
     * Generate the full article based on the configured options and responses.
     */
    public function streamData(object|array $streamResponse): ?string;
    
}
