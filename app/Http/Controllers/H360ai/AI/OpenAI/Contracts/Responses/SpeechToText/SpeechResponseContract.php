<?php

namespace Modules\OpenAI\Contracts\Responses\SpeechToText;

interface SpeechResponseContract extends SpeechToTextResponseContract
{
     /**
     * Get the generated text.
     *
     * @return string The generated text.
     */
    public function text(): string;
}