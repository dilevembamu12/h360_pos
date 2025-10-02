<?php

namespace Modules\OpenAI\Contracts\Responses\Code;

use Modules\OpenAI\Contracts\Responses\ResponseContract;

interface CodeResponseContract extends ResponseContract
{
    /**
     * Get the generated contents.
     *
     * @return array The generated contents.
     * 
     */
    public function content(): array;
}
