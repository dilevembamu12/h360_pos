<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_2_0;

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
        \DB::table('preferences')
            ->where('category', 'openai')
            ->whereIn('field', ['openai', 'stablediffusion', 'conversation_limit', 'google_api', 'stable_diffusion_engine', 'openai_engine', 'clipdrop_api'])
            ->delete();
    }
}
