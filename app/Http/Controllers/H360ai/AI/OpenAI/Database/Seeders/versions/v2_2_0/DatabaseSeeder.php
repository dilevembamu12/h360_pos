<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_2_0;

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
            ProviderManageMenusItemTableSeeder::class,
            PreferenceTableSeeder::class
        ]);
    }
}
