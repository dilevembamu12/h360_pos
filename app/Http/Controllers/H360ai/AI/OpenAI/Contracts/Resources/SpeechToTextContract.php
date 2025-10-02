<?php

namespace Modules\OpenAI\Contracts\Resources;

interface SpeechToTextContract
{
    /**
     * Provide the provider options for Speech To Text settings.
     *
     * @return array
     */
    public function speechToTextOptions(): array;
}
