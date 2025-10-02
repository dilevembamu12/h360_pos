<?php

namespace Modules\OpenAI\AiProviders\OpenAi\Resources;


class CodeDataProcessor
{
    /**
     * @var int $token which is used as default.
     *
     * This property holds an integer value used as a token identifier within the class.
     * It is initialized to 1024 by default.
     */
    private $token = 1024;

      /**
     * Prompt
     *
     * @var string
     */
    protected $prompt;

    /**
     * Description: Private property to store data.
     *
     * This property is used to store data within the class. It is intended
     * to be accessed only within the class itself and not from outside.
     *
     * @var array $data An array to store data.
     */
    private $data = [];

    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    /**
     * Returns an array of code options for the provider.
     *
     * @return array An array of code options with the following structure:
     * - type: string - The type of the option (e.g. "checkbox", "dropdown").
     * - label: string - The label of the option.
     * - name: string - The name of the option.
     * - value: mixed - The value of the option. For "dropdown" options, this is an array of values.
     */
    public function codeOptions(): array
    {
        return [
            [
                'type' => 'checkbox',
                'label' => 'Provider State',
                'name' => 'status',
                'value' => ''
            ],
            [
                'type' => 'dropdown',
                'label' => 'Language',
                'name' => 'language',
                'value' => [
                    'PHP',
                    'Java',
                    'Rubby',
                    'Python',
                    'C#',
                    'Go',
                    'Kotlin',
                    'HTML',
                    'Javascript',
                    'TypeScript',
                    'SQL',
                    'NoSQL'
                ]
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
                    'o1',
                    'o3-mini'
                ]
            ],
            [
                'type' => 'dropdown',
                'label' => 'Code Level',
                'name' => 'code_level',
                'value' => [
                    'Noob',
                    'Moderate',
                    'High'
                ]
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
            [
                "type" => "dropdown-with-image",
                "label" => "Code Language",
                "name" => "code_language",
                "value" => [
                    [
                        "label" => "PHP",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\php.png",
                    ],
                    [
                        "label" => "Java",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\java.png",
                    ],
                    [
                        "label" => "Rubby",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\rubby.jpg",
                    ],
                    [
                        "label" => "Python",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\python.png",
                    ],
                    [
                        "label" => "C#",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\c-sharp.png",
                    ],
                    [
                        "label" => "Go",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\go-lang.png", 
                    ],
                    [
                        "label" => "Kotlin",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\kotlin.png",
                    ],  
                    [
                        "label" => "HTML",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\html-5.png",
                    ],
                    [
                        "label" => "Javascript",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\js.png",
                    ],
                    [
                        "label" => "TypeScript",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\typescript.png",
                    ],
                    [
                        "label" => "SQL",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\mysql.png",
                    ],
                    [
                        "label" => "NoSQL",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\code\\no-sql.png",
                    ],
                ],
                "visibility" => true,
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
     * Generates a code prompt for the OpenAI model.
     *
     * @return array|string The generated code prompt.
     */
    public function codePrompt(): array|string
    {
        $model = data_get($this->data, 'model', 'gpt-4o');
        $language = data_get($this->data, 'language', moduleConfig('openai.codeLanguage'));
        $codeLevel = data_get($this->data, 'codeLevel', moduleConfig('openai.codeLevel'));
        $role = moduleConfig('openAi.roleBasedModels')[$model] ?? 'user';

        $baseMessage = "You are an expert coding assistant. Generate high-quality, well-structured code in " . $language . " at a " . $codeLevel . " level";

        $data = match ($role) {
            'developer' => [
                ['role' => 'developer', 'content' => $baseMessage . '. Formatting re-enabled.'],
                ['role' => 'user', 'content' => $this->data['prompt']],
            ],
            default => [
                ['role' => 'user', 'content' => $baseMessage . " regarding `" . $this->data['prompt'] . "`"],
            ],
        };

        return $this->prompt = [
            'model' => $model,
            'messages' => $data,
            'temperature' => 1,
            getMaxTokenKey($model) => (int) maxToken('code_openai'),
        ];

    }

    /**
     * Generates a code prompt for the OpenAI model.
     *
     * @return array The generated code prompt.
     */
    public function code(): array
    {
       return $this->codePrompt();
    }

}
