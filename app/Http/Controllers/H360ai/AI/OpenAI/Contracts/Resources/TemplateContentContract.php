<?php

namespace Modules\OpenAI\Contracts\Resources;


interface TemplateContentContract
{
    /**
     * Generate templates for the use case based on the given AI options.
     *
     * @param array $aiOptions The options for generating Article.
     */
    public function templateGenerate(array $aiOptions);
    
    /**
     * Generate the full article based on the configured options and responses.
     */
    public function templateStreamData($streamResponse);

    /**
     * Provide the provider options for template settings.
     *
     * @return array
     */
    public function templatecontentOptions(): array;
    
}
