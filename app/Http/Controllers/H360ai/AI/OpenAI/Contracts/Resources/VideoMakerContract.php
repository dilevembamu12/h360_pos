<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\ImageResponseContract;

interface VideoMakerContract
{
    public function generateVideo(array $aiOptions);
}
