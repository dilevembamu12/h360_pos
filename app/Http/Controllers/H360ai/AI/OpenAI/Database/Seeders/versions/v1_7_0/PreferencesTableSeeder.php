<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use Illuminate\Database\Seeder;

class PreferencesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('preferences')->upsert([
            [
                'category' => 'openai',
                'field' => 'stable_diffusion_engine',
                'value' => 'stable-diffusion-xl-1024-v1-0',
            ],
            [
                'category' => 'openai',
                'field' => 'openai_engine',
                'value' => 'dall-e-3',
            ],
        ], ['field']);
    }
}
