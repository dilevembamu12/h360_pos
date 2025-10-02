<?php

namespace Modules\OpenAI\Contracts\Responses\Template;

use Modules\OpenAI\Contracts\Responses\ResponseContract;

interface TemplateResponseContract extends ResponseContract
{
    /**
     * Get the generated contents.
     *
     * @return array The generated contents.
     * 
     */
    public function response(): string;
}
