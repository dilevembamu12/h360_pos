<?php

namespace Modules\OpenAI\Contracts\Resources;

use Modules\OpenAI\Contracts\Responses\AiDocChat\AskQuestionResponseContract;

interface AiDocChatContract
{
    /**
     * Provide the provider options for ai doc chat settings.
     *
     * @return array
     */
    public function aiDocChatOptions(): array;

    /**
     * Ask a question using the provided AI options.
     *
     * @param array $aiOptions Options for AI configuration.
     * @return AskQuestionResponseContract The answer to the question.
     */
    public function askQuestion(array $aiOptions): AskQuestionResponseContract;
}
