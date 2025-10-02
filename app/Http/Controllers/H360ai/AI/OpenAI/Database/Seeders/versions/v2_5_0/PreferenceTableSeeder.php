<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_5_0;

use Illuminate\Database\Seeder;

class PreferenceTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('preferences')->upsert([
            [
                'category' => 'openai',
                'field' => 'user_permission',
                'value' => '{"hide_template":"0","hide_image":"0","hide_code":"0","hide_speech_to_text":"0","hide_text_to_speech":"0","hide_chat":"0"}',
            ]
        ], ['field']);
    }
}
