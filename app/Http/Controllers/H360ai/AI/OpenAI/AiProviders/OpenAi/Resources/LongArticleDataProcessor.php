<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Resources;

use Str;

class LongArticleDataProcessor
{
    private $data = [];

    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    public function longarticleOptions(): array
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
                    'gpt-4',
                    'gpt-3.5-turbo',
                    'gpt-4o',
                    'gpt-4o-mini',
                    'o1-preview',
                    'o1-mini',
                    'o3-mini'
                ]
            ],
            [
                'type' => 'dropdown',
                'label' => 'Tones',
                'name' => 'tone',
                'value' => [
                    'Normal', 'Formal', 'Casual', 'Professional', 'Serious', 'Friendly', 'Playful', 'Authoritative', 'Empathetic', 'Persuasive', 'Optimistic', 'Sarcastic', 'Informative', 'Inspiring', 'Humble', 'Nostalgic', 'Dramatic'
                ]
            ],
            [
                'type' => 'dropdown',
                'label' => 'Languages',
                'name' => 'language',
                'value' => [
                    'English', 'French', 'Arabic', 'Byelorussian', 'Bulgarian', 'Catalan', 'Estonian', 'Dutch'
                ]
            ],
            [
                'type' => 'dropdown',
                'label' => 'Frequency Penalty',
                'name' => 'frequency_penalty',
                'value' => [
                    0, 0.5, 1, 1.5, 2  
                ],
                'default_value' => 0
            ],
            [
                'type' => 'dropdown',
                'label' => 'Presence Penalty',
                'name' => 'presence_penalty',
                'value' => [
                    0, 0.5, 1, 1.5, 2  
                ],
                'default_value' => 0
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
                'type' => 'dropdown',
                'label' => 'Top P',
                'name' => 'top_p',
                'value' => [
                    0, 0.25, 0.50, 0.75, 1
                ],
                'default_value' => 1
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
        return [];
    }

    public function titlePrompt(): string
    {
        return filteringBadWords("Generate " . ($this->data['number_of_title'] == '1' ? 'only one' :  $this->data['number_of_title']) ." seo friendly ". Str::plural('title', $this->data['number_of_title']) ." in " . ($this->data['options']['language'] ?? 'English'). " language based on this topic & keywords in " . ($this->data['options']['tone'] ?? 'Normal') . " tone. Topic: " . $this->data['topic'] . ", Keywords: " . $this->data['keywords'] . ". ". ($this->data['number_of_title'] == '1' ? "The title" : "Each titles") ." must be an array element, give the output as an array. No addtional text before and after array [] brackets.");
    }

    public function titleDataOptions(): array
    {
        return [
            'model' => $this->data['options']['model'] ?? 'gpt-4',
            'messages' => [
                [
                    'role' => 'user', 
                    'content' => $this->titlePrompt(),
                ]
            ],
            "temperature" => (int) $this->data['options']['temperature'] ?? 1,
            "n" => 1,
            "frequency_penalty" => (int) $this->data['options']['frequency_penalty'] ?? 0,
            "presence_penalty" => (int) $this->data['options']['presence_penalty'] ?? 0,
            "top_p" => (int) $this->data['options']['top_p'] ?? 1,
        ];
    }

    public function titleOptions(): array
    {
        return $this->titleDataOptions();
    }

    public function outlinePrompt(): string
    {
        return filteringBadWords("Generate section headings only to write a blog in " . ($this->data['options']['language'] ?? 'English') . " language in " . ($this->data['options']['tone'] ?? 'Normal') . " tone based on this title & keywords. Title: " . $this->data['title'] . ", Keywords: " . $this->data['keywords'] . ". Each section headings must be an array element, give the output as an array. No addtional text before and after array [] brackets. Please do not prefix array elements with numbers and enclose array elements in double quotes.");

    }

    public function outlineDataOptions(): array
    {
        return [
            'model' => $this->data['options']['model'] ?? 'gpt-4',
            'messages' => [
                [
                    'role' => 'user', 
                    'content' => $this->outlinePrompt(),
                ]
            ],
            "temperature" => (int) $this->data['options']['temperature'] ?? 1,
            "n" => isset($this->data['number_of_outlines']) ? (int) $this->data['number_of_outlines'] : 1,
            "frequency_penalty" => (int) $this->data['options']['frequency_penalty'] ?? 0,
            "presence_penalty" => (int) $this->data['options']['presence_penalty'] ?? 0,
            "top_p" => (int) $this->data['options']['top_p'] ?? 1,
        ];
    }

    public function outlineOptions(): array
    {
        return $this->outlineDataOptions();
    }
    
    public function articlePrompt(): string
    {
        return filteringBadWords("This is the title: " . $this->data['title'] . ". These are the keywords: " . $this->data['keywords'] . ". This is the Heading list: " . $this->data['outlines'] . ". Expand each Heading section to generate article in " . ($this->data['options']['language'] ?? 'English') . " language in ". ($this->data['options']['tone'] ?? 'Normal') ." tone. Do not add other Headings or write more than the specific Headings in Heading list. Give the Heading output in bold font.");

    }

    public function articleDataOptions(): array
    {
        return [
            'model' => $this->data['options']['model'] ?? 'gpt-4',
            'messages' => [
                [
                    'role' => 'user', 
                    'content' => $this->articlePrompt(),
                ]
            ],
            "temperature" => (int) $this->data['options']['temperature'] ?? 1,
            "n" => isset($this->data['number_of_outlines']) ? (int) $this->data['number_of_outlines'] : 1,
            "frequency_penalty" => (int) $this->data['options']['frequency_penalty'] ?? 0,
            "presence_penalty" => (int) $this->data['options']['presence_penalty'] ?? 0,
            "top_p" => (int) $this->data['options']['top_p'] ?? 1,
            getMaxTokenKey(data_get($this->data['options'], 'model', 'gpt-4')) => (int) maxToken('longarticle_openai'),
        ];
    }

    public function articleOptions(): array
    {
        return $this->articleDataOptions();
    }

}
