<?php

namespace Modules\OpenAI\Contracts\Resources;


interface CodeContract
{

    /**
     * Generate code based on the given AI options.
     *
     * @param array $aiOptions The options for generating codes.
     */
    public function code(array $aiOptions);
    
}
