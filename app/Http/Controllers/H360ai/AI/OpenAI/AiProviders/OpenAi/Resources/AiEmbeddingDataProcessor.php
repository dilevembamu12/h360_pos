<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Resources;

class AiEmbeddingDataProcessor
{
    private $data = [];

    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    public function aiembeddingOptions(): array
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
                    'text-embedding-ada-002',
                    'text-embedding-3-large',
                    'text-embedding-3-small'
                ],
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
        return [];
    }

    public  function aiEmbeddingDataOptions(): array
    {
        return [
            'model' => $this->data['model'] ?? 'text-embedding-ada-002', // Note: OpenAI's model
            'input' => $this->data['text']
        ];
    }
}