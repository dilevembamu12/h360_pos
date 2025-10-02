<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_8_0;
use Illuminate\Database\Seeder;
use Modules\OpenAI\Entities\FeaturePreference;


class FeaturePreferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        \DB::table('feature_preferences')->upsert([
            [
                'name' => 'Ai Plagiarism',
                'slug' => 'ai_plagiarism',
            ],
            [
                'name' => 'Ai Detector',
                'slug' => 'ai_detector',
            ]
        ], [ 'slug' ]);

        $feature = FeaturePreference::where('slug', 'ai_detector')->first();

        if (!isset($feature->settings)) {
            $commonData = [
                'owner_type' => 'Modules\OpenAI\Entities\FeaturePreference',
                'owner_id' => $feature->id,
                'type' => 'string'
            ];
        
            $metas = [
                [
                    'key' => 'settings',
                    'value' => json_encode([
                        'file_size' => '10',
                        'feature_options' => [
                            'file_upload' => 'on',
                            'content_description' => 'on'
                        ]
                    ])
                ]
            ];
        
            $dataToInsert = array_map(function($meta) use ($commonData) {
                return array_merge($commonData, $meta);
            }, $metas);
        
            \DB::table('feature_preference_metas')->upsert($dataToInsert, ['owner_type', 'owner_id', 'key'], ['key']);
        }

        $objectFile = \DB::table('object_files')
            ->where('object_type', 'feature_preferences')
            ->value('file_id');

        $feature = FeaturePreference::where('slug', 'chatbot')->first();

        if ($feature) {
            $generalOptions = json_decode($feature->general_options, true);

            if (!isset($generalOptions['default_floating_image'])) {
                // Update the required fields
                $generalOptions['default_avatar'] = $objectFile;

                $fileId = \DB::table('files')->insertGetId([
                    'params' => '{"size":4.0107421875,"type":"png"}',
                    'object_type' => 'png',
                    'object_id' => null,
                    'uploaded_by' => 1,
                    'file_name' => '20241008\2bcf18ce59a7b45c4bca7a5327333f41.png',
                    'file_size' => 4.01,
                    'original_file_name' => 'img-robot-face.png',
                ]);
    
                $generalOptions['default_floating_image'] = $fileId;
                // Update the feature with the modified general_options
                $feature->general_options = json_encode($generalOptions);
                $feature->save();
        }
            }
            
    }
}
