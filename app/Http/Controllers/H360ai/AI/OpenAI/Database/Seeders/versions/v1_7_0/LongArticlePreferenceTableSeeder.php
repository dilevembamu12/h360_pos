<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class LongArticlePreferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('preferences')->upsert([
            [
                'category' => 'long_article',
                'field' => 'long_article_provider',
                'value' => 'OpenAi',
            ],
            [
                'category' => 'long_article',
                'field' => 'long_article_model',
                'value' => 'gpt-4',
            ],
            [
                'category' => 'long_article',
                'field' => 'long_article_language',
                'value' => '["English","French","Chinese","Arabic","Byelorussian","Bulgarian","Catalan","Estonian", "Dutch"]',
            ],
            [
                'category' => 'long_article',
                'field' => 'long_article_tone',
                'value' => '["Normal","Studio","Warm","Cold","Ambient","Neon","Foggy","Dramatic","Bold","Casual","Professional","Friendly"]'
            ],
            [
                'category' => 'long_article',
                'field' => 'long_article_temperature',
                'value' => '["Optimal","Low","Medium","High"]',
            ],
            [
                'category' => 'long_article',
                'field' => 'long_article_frequency_penalty',
                'value' => 0,
            ],
            [
                'category' => 'long_article',
                'field' => 'long_article_presence_penalty',
                'value' => 0,
            ]
        ], ['field']);
        
        $userPermission =  DB::table('preferences')->where('field', 'user_permission')->first();
        

        if ($userPermission) {

            $value = json_decode($userPermission->value, true) + ['hide_long_article' => '0'];
            DB::table('preferences')->where('field', 'user_permission')->update(['value' => $value]);
        }
        
    }
}
