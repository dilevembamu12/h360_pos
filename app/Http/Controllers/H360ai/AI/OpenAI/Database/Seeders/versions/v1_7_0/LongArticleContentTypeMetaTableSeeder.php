<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use DB;
use Illuminate\Database\Seeder;
use Modules\OpenAI\Entities\ContentType;

class LongArticleContentTypeMetaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contentType = \DB::table('content_types')->where('slug', 'long_article')->first();

        if ($contentType) {
            $contentTypeMetas = [
                ['long_article_providers', '["OpenAi"]'],
                ['long_article_models', '["gpt-4","gpt-3.5-turbo"]' ],
                ['long_article_languages', '["English","French","Chinese","Arabic","Byelorussian","Bulgarian","Catalan","Estonian", "Dutch"]'],
                ['long_article_tones', '["Normal","Studio","Warm","Cold","Ambient","Neon","Foggy","Dramatic","Bold","Casual","Professional","Friendly"]'],
                ['long_article_temperature','["Optimal","Low","Medium","High"]'],
                ['long_article_frequency_penalty','0'],
                ['long_article_presence_penalty', '0'],
            ];

            foreach ($contentTypeMetas as $meta) {
                if (! DB::table('content_types_meta')->where('key', $meta[0])->exists()) {

                    DB::table('content_types_meta')->insert([
                        'content_type_id' => $contentType->id,
                        'name' => 'long_article',
                        'key' => $meta[0],
                        'value' => $meta[1]
                    ]);

                }
            }
        }
        
    }
}
