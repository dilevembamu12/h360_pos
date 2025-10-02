<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_8_0;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            FeaturePreferenceTableSeeder::class,
            PermissionTableSeeder::class,
            PreferenceTableSeeder::class,
            ChatBotsTableSeeder::class,
        ]);
    }
}
