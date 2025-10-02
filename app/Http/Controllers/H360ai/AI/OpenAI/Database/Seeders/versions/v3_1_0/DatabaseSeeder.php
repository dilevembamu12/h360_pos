<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_1_0;

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
            TeamMemberSeeder::class,
        ]);
    }
}
