<?php

namespace Modules\OpenAI\Contracts\Resources;

interface ChatbotContract
{

    /**
     * Provide the ai provider options for Chatbot provider settings.
     *
     * @return array
     */
    public function chatbotOptions(): array;

}
