<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_8_0;
use Modules\OpenAI\Database\Seeders\versions\v1_7_0\DatabaseSeeder as DatabaseSeederV17;

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
            DatabaseSeederV17::class,
            ContentTypeTableSeeder::class,
            PreferencesTableSeeder::class
        ]);
    }
}
