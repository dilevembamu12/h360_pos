<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_6_0;

use Illuminate\Database\Seeder;

class PreferenceTableSeeder extends Seeder
{
    public function run()
    {
        $userPermission =  \DB::table('preferences')->where('field', 'user_permission')->first();

        if ($userPermission) {

            $value = json_decode($userPermission->value, true) + ['hide_plagiarism' => '0'];
            \DB::table('preferences')->where('field', 'user_permission')->update(['value' => $value]);
        }
    }
}
