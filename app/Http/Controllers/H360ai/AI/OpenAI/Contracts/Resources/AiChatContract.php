<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\AiChat\AiChatResponseContract;

interface AiChatContract
{
    /**
     * Provide the provider options for aiChat settings.
     *
     * @return array
     */
    public function aiChatOptions(): array;

    /**
     * Define the method signature for the aiChat function.
     *
     * @param array $aiOptions The options for the chatbot.
     */
    public function aiChat(array $aiOptions): AiChatResponseContract;
}
