<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_7_0;
use Illuminate\Database\Seeder;


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
                'name' => 'Document',
                'slug' => 'document',
            ],
            [
                'name' => 'Image Maker',
                'slug' => 'image_maker',
            ],
            [
                'name' => 'Code Writer',
                'slug' => 'code_writer',
            ],
            [
                'name' => 'Speech to text',
                'slug' => 'speech_to_text'
            ],
            [
                'name' => 'Text To Speech',
                'slug' =>  'text_to_speech'
            ],
            [
                'name' => 'Long Article',
                'slug' => 'long_article',
            ],
            [
                'name' => 'Chatbot',
                'slug' => 'chatbot',
            ],
            [
                'name' => 'Ai Doc Chat',
                'slug' => 'ai_doc_chat',
            ]
        ], [ 'slug' ]);

        $feature = \DB::table('feature_preferences')->where('slug', 'chatbot')->first();

        if ($feature) {
            $commonData = [
                'owner_type' => 'Modules\OpenAI\Entities\FeaturePreference',
                'owner_id' => $feature->id,
                'type' => 'string'
            ];
        
            $metas = [
                [
                    'key' => 'general_options',
                    'value' => json_encode([
                        'languages' => [
                            'English', 'Bengali', 'French', 'Chinese', 'Arabic', 'Byelorussian', 'Bulgarian', 'Catalan', 'Estonian', 'Dutch', 'Russian', 'Spanish', 'Portuguese', 'Polish', 'German', 'Sweden'
                        ],
                        'default_avatar' => null
                    ])
                ],
                [
                    'key' => 'theme_options',
                    'value' => json_encode([
                        'color' => [
                            '#9163dd', '#e22861', '#fcca19', '#ff1493', '#2c2c2c', 
                            '#5af457', '#5707cf', '#f2ec36'
                        ]
                    ])
                ],
                [
                    'key' => 'settings',
                    'value' => json_encode([
                        'conversation' => 'on',
                        'file_size' => '10',
                        'file_limit' => '5',
                        'url_limit' => '5',
                        'training_options' => [
                            'file_upload' => 'on',
                            'website_url' => 'on',
                            'pure_text' => 'on'
                        ]
                    ])
                ]
            ];
        
            $dataToInsert = array_map(function($meta) use ($commonData) {
                return array_merge($commonData, $meta);
            }, $metas);
        
            \DB::table('feature_preference_metas')->upsert($dataToInsert, ['owner_type', 'owner_id', 'key'], ['key']);

            $file = \DB::table('object_files')
                ->where([
                    'object_type' => 'feature_preferences',
                    'object_id' => $feature->id
                ])->first();

            if (!$file) {
                $fileData = [
                    'params' => '{"size":2.841796875,"type":"png"}',
                    'object_type' => 'png',
                    'object_id' => null,
                    'uploaded_by' => 1,
                    'file_name' => '20240703/38ac8dce684255efc0f6c2d14590a76e.png',
                    'file_size' => 2.84,
                    'original_file_name' => 'default.png',
                ];

                $fileId = \DB::table('files')->insertGetId($fileData);

                \DB::table('object_files')->upsert([
                    [
                        'object_type' => 'feature_preferences',
                        'object_id' => $feature->id,
                        'file_id' => $fileId
                    ]
                ], ['object_type', 'object_id'], ['file_id']);  // Use unique keys for conflict, update file_id on conflict
            }

        }

        $aiDocChatFeature = \DB::table('feature_preferences')->where('slug', 'ai_doc_chat')->first();

        if ($aiDocChatFeature) {
            \DB::table('feature_preference_metas')->upsert([
                [
                    'owner_type' => 'Modules\OpenAI\Entities\FeaturePreference',
                    'owner_id' => $aiDocChatFeature->id,
                    'type' => 'string',
                    'key' => 'general_options',
                    'value' => json_encode([
                        "user_access_disable" => "on",
                        "provider" => "openai",
                        "model" => "text-embedding-ada-002"
                    ])
                ]
            ], ['owner_type', 'owner_id', 'key'], ['key']);
        }
    }
}
