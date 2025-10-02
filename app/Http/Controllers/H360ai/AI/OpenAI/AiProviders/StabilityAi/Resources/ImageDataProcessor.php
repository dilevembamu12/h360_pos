<?php

namespace Modules\OpenAI\AiProviders\StabilityAi\Resources;

class ImageDataProcessor
{
    private $data = [];

    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    public function rules()
    {
        return [];
    }

    public function imageOptions(): array
    {
        return [
            [
                'type' => 'checkbox',
                'label' => 'Provider State',
                'name' => 'status',
                'value' => '', 
                'visibility' => true
            ],
            [
                'type' => 'text',
                'label' => 'Provider',
                'name' => 'provider',
                'value' => 'Stabilityai',
                'visibility' => false
            ],
            [
                'type' => 'dropdown',
                'label' => 'Service',
                'name' => 'service',
                'value' => [
                    'text-to-image',
                    'image-to-image',
                ],
                'default_value' => 'text-to-image',
                'visibility' => true,
                'required' => true
            ],
            [
                'type' => 'dropdown',
                'label' => 'Models',
                'name' => 'model',
                'value' => [
                    'stable-diffusion-xl-1024-v1-0',
                    'stable-diffusion-v1-6'
                ],
                'default_value' => 'stable-diffusion-xl-1024-v1-0',
                'visibility' => true,
                'required' => true
            ],
            [
                'type' => 'dropdown',
                'label' => 'Variant',
                'name' => 'variant',
                'value' => [
                    1, 2, 3
                ],
                'default_value' => 1,
                'visibility' => true,
                "required" => true,
            ],
            [
                'type' => 'dropdown',
                'label' => 'Size',
                'name' => 'size',
                'value' => [
                    '1024x1024',
                    '1152x896',
                    '896x1152',
                    '1216x832',
                    '1344x768',
                    '768x1344',
                    '1536x640',
                    '640x1536',
                ],
                'default_value' => '1024x1024',
                'visibility' => true,
                'required' => true
            ],
            [
                "type" => "dropdown",
                "label" => "Art Style",
                "name" => "art_style",
                "value" => [
                    'Normal',
                    '3D Model',
                    'Analog Film',
                    'Anime',
                    'Cinematic',
                    'Comic Book',
                    'Digital Art',
                    'Enhance',
                    'Fantacy Art',
                    'Icometric',
                    'Line Art',
                    'Low Poly',
                    'Modeling Compound',
                    'Neon Punk',
                    'Origami',
                    'Photographic',
                    'Pixel Art',
                    'Tile Texture',
                    'Water Color'
                ],
                "default" => "Normal",
                "visibility" => true
            ],
            [
                "type" => "dropdown",
                "label" => "Light Effect",
                "name" => "light_effect",
                "value" => [
                    "Normal",
                    "Studio",
                    "Warm",
                    "Cold",
                    "Ambient",
                    "Neon",
                    'Foggy'
                ],
                "default" => "Normal",
                "visibility" => true,
            ],
            [
                'type' => 'file',
                'label' => 'File',
                'name' => 'file',
                'value' => '',
                'visibility' => true
            ],
            [
                "type" => "dropdown-with-image",
                "label" => "Image Art Style",
                "name" => "image_art_style",
                "value" => [
                    [
                        "label" => "Normal",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\normal.jpg",
                    ],
                    [
                        "label" => "3D Model",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\3d-animation.png",
                    ],
                    [
                        "label" => "Analog Film",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\analog-film.jpg",
                    ],
                    [
                        "label" => "Anime",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\anime.png",
                    ],
                    [
                        "label" => "Cinematic",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\cinematic.jpg",
                    ],
                    [
                        "label" => "Comic Book",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\comic.png", 
                    ],
                    [
                        "label" => "Digital Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\digital-art.png",
                    ],  
                    [
                        "label" => "Enhance",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\enhance.png",
                    ],
                    [
                        "label" => "Fantasy Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\fantasy.png",
                    ],
                    [
                        "label" => "Isometric",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\isometric.jpg",
                    ],
                    [
                        "label" => "Line Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\line-art.png",
                    ],
                    [
                        "label" => "Low Poly",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\low-poly.png",
                    ],
                    [
                        "label" => "Modeling Compound",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\modeling-compound.png",
                    ],
                    [
                        "label" => "Neon Punk",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\neon-punk.png",
                    ],
                    [
                        "label" => "Origami",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\origami.jpg",
                    ],
                    [
                        "label" => "Photographic",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\photographic.jpg",
                    ],
                    [
                        "label" => "Pixel Art",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\pixel-art.jpg",
                    ],
                    [
                        "label" => "Tile Texture",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\tile-texture.jpg",  
                    ],  
                    [
                        "label" => "Water Color",
                        "url" => "Modules\\OpenAI\\Resources\\assets\\image\\art-style\\water-color.png",
                    ]
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

    public function imageDataOptions()
    {
        return [
            'model' => data_get($this->data['options'], 'model', 'stable-diffusion-xl-1024-v1-0'),
            "service" => data_get($this->data['options'], 'service', 'text-to-image'),
            "prompt" => $this->data['prompt'],
            "samples" => data_get($this->data['options'], 'variant', 1),
            "height" => (int) isset($this->data['options']['size']) ? explode("x", $this->data['options']['size'])[1] : '1024',
            "width" => (int) isset($this->data['options']['size']) ? explode("x", $this->data['options']['size'])[0] : '1024',
            'art_style' => data_get($this->data['options'],'art_style', 'Normal'),
            'light_effect' => data_get($this->data['options'],'light_effect', 'Normal'),
            'tone' => data_get($this->data['options'],'tone', 'Normal'),
            "cfg_scale" => 7,
            "steps" => 30,
            "clip_guidance_preset" => 'FAST_BLUE',
            'image_file' => isset($this->data['options']['file']) ? file_get_contents($this->data['options']['file']) : null
        ];
    }

    public function imageData(): array
    {
        return $this->imageDataOptions();
    }

    public function prepareFile()
     {
        $uploadedFile = $this->data['options']['file'];
        $originalFileName = $uploadedFile->getClientOriginalName();
        return new \CURLFile($uploadedFile->getRealPath(), $uploadedFile->getMimeType(), $originalFileName);
     }
}
