<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_2_0;

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
            ProviderManagerTableSeeder::class,
            ProviderManagerSeeder::class,
            MenusItemTableSeeder::class,
            PermissionTableSeeder::class,
            PreferencesTableSeeder::class
        ]);
    }
}
