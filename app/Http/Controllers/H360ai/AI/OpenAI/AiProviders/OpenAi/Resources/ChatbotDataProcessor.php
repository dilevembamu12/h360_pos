<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Resources;

use Str;

class ChatbotDataProcessor
{
    private $data = [];

    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    public function chatbotOptions(): array
    {
        return [
            [
                'type' => 'checkbox',
                'label' => 'Provider State',
                'name' => 'status',
                'value' => '',
            ],
            [
                'type' => 'text',
                'label' => 'Provider',
                'name' => 'provider',
                'value' => 'OpenAi'
            ],
            [
                'type' => 'dropdown',
                'label' => 'Models',
                'name' => 'model',
                'value' => [
                    'gpt-4o-mini',
                    'gpt-4o',
                    'gpt-4',
                    'gpt-3.5-turbo',
                    'o1-preview',
                    'o1-mini',
                    'o1',
                    'o3-mini'
                ]
            ],
            
        ];
    }
    
    /**
     * Retrieve the validation rules for the current data processor.
     * 
     * @return array An array of validation rules.
     */
    public function validationRules()
    {
        return [];
    }
    /**
     * Returns a prompt for asking a question, filtering out bad words.
     *
     * @return string The prompt for asking a question with bad words filtered out.
     */
    public function askQuestionPrompt(): string
    {
        $context = data_get($this->data, 'content', '');

        return filteringBadWords(
            "Respond to the user's query based on the provided context: '{$context}'. 
            If the context lacks sufficient information, reply with: 'I'm sorry, but I don't have this information.'. 
            Avoid generating unrelated content or empty responses. Generate response based on " . (data_get($this->data, 'language', 'English') ) . " language and in " . (data_get($this->data, 'tone',  'Normal')) . " tone."
        );
    }
    /**
     * Returns an array of options for asking a question.
     *
     * @return array
     */
    public function askQuestionDataOptions(): array
    {
        $model = data_get($this->data, 'model', 'gpt-4o');
        $role = moduleConfig('openAi.roleBasedModels')[$model] ?? 'user';

        $message = [];

        $message = match ($role) {
            'developer' => [
                ['role' => 'developer', 'content' => $this->askQuestionPrompt() . ' Formatting re-enabled.'],
                ['role' => 'user', 'content' =>  $this->data['prompt']],
            ],
            default => [
                ['role' => 'user', 'content' => $this->askQuestionPrompt() . " " . $this->data['prompt'] ],
            ],
        };

        return [
            'model' => $model,
            'messages' => $message,
            'temperature' => isset($this->data['temperature']) && $this->data['temperature'] ? (float) $this->data['temperature'] : 1,
            getMaxTokenKey(data_get($this->data, 'model', 'gpt-4')) => (int) maxToken('chatbot_openai')
        ];
    }
}
