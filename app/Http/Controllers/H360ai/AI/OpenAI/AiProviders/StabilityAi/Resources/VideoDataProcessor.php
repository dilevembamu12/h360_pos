<?php

namespace Modules\OpenAI\AiProviders\StabilityAi\Resources;

class VideoDataProcessor
{
    private $data = [];

    public function __construct(array $aiOptions = [])
    {
        $this->data = $aiOptions;
    }

    public function videoOptions(): array
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
                'type' => 'number',
                'label' => 'Motion Intensity',
                'name' => 'motion_intensity',
                'min' => 0,
                'max' => 255,
                'value' => 160,
                'tooltip' => __('Lower values generally result in less motion in the output video, while higher values generally result in more motion. The range is 0 ~ 255'),
                'visibility' => true
            ],
            [
                'type' => 'number',
                'label' => 'Seed',
                'name' => 'seed',
                'value' => 0,
                'min' => 0,
                'max' => 4294967294,
                'tooltip' => __('A specific value from 0 to 4294967294 that is used to guide the randomness of the generation.'),
                'visibility' => true
            ],
            [
                'type' => 'slider',
                'label' => 'Image Strength',
                'name' => 'image_strength',
                'min' => 0,
                'max' => 10,
                'value' => 6,
                'required' => true,
                'tooltip' => __('A specific value from 0 to 10 to express how strongly the video sticks to the original image.'),
                'visibility' => true
            ]
        ];
    }

    public function generateVideo(): array
    {
        return  [
            "image" => $this->data['image'],
            "cfg_scale" => $this->data['options']['image_strength'],
            "motion_bucket_id" => $this->data['options']['motion_intensity'],
            "seed" => $this->data['options']['seed']
        ];
    }

    /**
     * Retrieve the validation rules for the current data processor.
     * 
     * @return array An array of validation rules.
     */
    public function videoValidationRules()
    {
        $validationRules['image_strength'] = 'required';
        $validationRules['motion_intensity'] = 'required';
        $validationRules['seed'] = 'required';
        $validationMessage = [
            'image_strength.required' => __('Image Strength is required'),
            'motion_intensity.required' => __('Motion Intensity is required'),
            'seed.required' => __('Seed is required'),
        ];
        return [
            $validationRules,
            $validationMessage
        ];
    }
}
