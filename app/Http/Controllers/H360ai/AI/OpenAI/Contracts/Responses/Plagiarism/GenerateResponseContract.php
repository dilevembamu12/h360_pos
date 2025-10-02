<?php

namespace Modules\OpenAI\Contracts\Responses\Plagiarism;

interface GenerateResponseContract extends PlagiarismResponseContract
{
    /**
     * Get the generated response.
     *
     * @return array The generated response.
     */
    public function content(): array;

    /**
     * Get the word count.
     *
     * @return int The total word count.
     */
    public function words(): int;

    /**
     * Get the expense associated with generating the response.
     *
     * @return int The expense in some currency (e.g., dollars).
     */
    public function expense(): int;
}
