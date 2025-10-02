<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_8_0;

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
                'field' => 'clipdrop_api',
                'value' => '70e3d86eed1443cd2c628b6723e8084e987846dd4d5d292aa1690642aaa665cabfesadasdsadasdsadwqr',
            ],
        ], ['category', 'field']);
    }
}
