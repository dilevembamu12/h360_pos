<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Resources;

use Str;

class AiDocChatDataProcessor
{
    private $data = [];

    /**
     * Constructor for the AiDocChatDataProcessor class.
     * Initializes the data property with the provided AI options.
     *
     * @param array $aiOptions The AI options to initialize the data property.
     */
    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    /**
     * Returns an array of options for OpenAI Provider.
     *
     * @return array The array of AI document chat options.
     */
    public function aiDocChatOptions(): array
    {
        return [
            [
                'type' => 'checkbox',
                'label' => 'Provider State',
                'name' => 'status',
                'value' => 'on',
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
                ],
                'required' => true
            ],
            [
                'type' => 'dropdown',
                'label' => 'Temperature',
                'name' => 'temperature',
                'value' => [
                    0, 0.5, 1, 1.5, 2
                ],
                'default_value' => 1,
            ],
            [
                'type' => 'number',
                'label' => 'Max Tokens',
                'name' => 'max_tokens',
                'min' => 1,
                'max' => 4096,
                'value' => 2048,
                'visibility' => true,
                'required' => true
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
        return [
            'max_tokens' => 'required|integer|min:1|max:4096',
        ];
    }

    /**
     * Returns an array of AI doc chat data options.
     *
     * @return array The array containing the model and input data options.
     */
    public  function aiDocChatDataOptions(): array
    {
        return [
            'model' => data_get($this->data, 'embedding_model', 'text-embedding-ada-002'),
            'input' => $this->data['text']
        ];
    }

    /**
     * Returns a prompt for asking a question, filtering out bad words.
     *
     * @return string The prompt for asking a question with bad words filtered out.
     */
    public function askQuestionPrompt(): string
    {
        $context = data_get($this->data, 'context', '');
        $language = data_get($this->data, 'language', null);
        $tone = data_get($this->data, 'tone', null);
        $commonText = 'Generate response based on';
        $string = ' ';
        if ($language && $tone) {
            $string .= "{$commonText} {$language} language and in {$tone} tone.";
        } elseif ($language) {
            $string .= "{$commonText} {$language} language.";
        } elseif ($tone) {
            $string .= "{$commonText} {$tone} tone.";
        }
        $prompt = "Respond to the user's query based on the provided context: '{$context}'. If the context lacks sufficient information, reply with: 'I'm sorry, but I don't have this information.'. Avoid generating unrelated content or empty responses. {$string}";
        return filteringBadWords($prompt);
    }

    /**
     * Returns an array of options for asking a question.
     *
     * @return array
     */
    public function askQuestionDataOptions(): array
    {
        $model = data_get($this->data, 'chat_model', 'gpt-4o');
        $role = moduleConfig('openAi.roleBasedModels')[$model] ?? 'user';

        $message = [];

        $message = match ($role) {
            'developer' => [
                ['role' => 'developer', 'content' => 'Formatting re-enabled.' . $this->askQuestionPrompt()],
                ['role' => 'user', 'content' =>  $this->data['prompt']],
            ],
            default => [
                ['role' => 'user', 'content' => $this->askQuestionPrompt() . " " . $this->data['prompt'] ],
            ],
        };
        
        return [
            'model' => $model,
            'messages' => $message,
            "temperature" => $this->getTemperatureValue(),
            getMaxTokenKey($model) => (int) maxToken('aidocchat_openai'),

        ];
    }

    /**
     * Retrieves the temperature value for the current chat model.
     *
     * This method checks if the current chat model is part of a predefined list of fixed temperature models.
     * If it is, the method returns a fixed temperature value of 1.
     * Otherwise, it retrieves the user-defined temperature from the provided data, defaulting to 1 if not set.
     *
     * @return int|float The temperature value to be used for the chat model.
     */
    public function getTemperatureValue(): int|float
    {
        $fixedTemperatureModels = moduleConfig('openai.fixedTemperatureModels');
        // Get the current model from the data
        $model = data_get($this->data, 'chat_model', 'gpt-4');

        // Return 1 if the model is in the fixed temperature list
        if (in_array($model, $fixedTemperatureModels)) {
            return 1;
        }

        // Otherwise, return the user-defined temperature or fallback to 1
        return isset($this->data['temperature']) ? (int) $this->data['temperature'] : 1;
    }

    /**
     * Returns the options for asking a question by delegating to the askQuestionDataOptions method.
     *
     * @return array The options for asking a question.
     */
    public function askQuestionOptions(): array
    {
        return $this->askQuestionDataOptions();
    }

}
