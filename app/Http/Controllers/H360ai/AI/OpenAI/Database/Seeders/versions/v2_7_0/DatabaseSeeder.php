<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_7_0;

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
            FeaturePreferenceMenusItemTableSeeder::class,
            FeaturePreferenceTableSeeder::class,
            PreferenceTableSeeder::class,
            PermissionTableSeeder::class
        ]);
    }
}
