<?php

namespace Modules\OpenAI\Database\Seeders;

use Modules\OpenAI\Database\Seeders\ArchivesMetaTableSeeder;
use Modules\OpenAI\Database\Seeders\ArchivesTableSeeder;
use Modules\OpenAI\Database\Seeders\ChatBotsMetaTableSeeder;
use Modules\OpenAI\Database\Seeders\ChatBotsTableSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use Modules\OpenAI\Database\Seeders\versions\v1_2_0\DatabaseSeeder as DatabaseSeederV12;

use Modules\OpenAI\Database\Seeders\versions\v1_4_0\DatabaseSeeder as DatabaseSeederV14;
use Modules\OpenAI\Database\Seeders\versions\v1_5_0\DatabaseSeeder as DatabaseSeederV15;
use Modules\OpenAI\Database\Seeders\versions\v1_6_0\DatabaseSeeder as DatabaseSeederV16;
use Modules\OpenAI\Database\Seeders\versions\v1_7_0\DatabaseSeeder as DatabaseSeederV17;

use Modules\OpenAI\Database\Seeders\versions\v1_8_0\DatabaseSeeder as DatabaseSeederV18;
use Modules\OpenAI\Database\Seeders\versions\v2_0_0\DatabaseSeeder as DatabaseSeederV20;

use Modules\OpenAI\Database\Seeders\versions\v2_2_0\DatabaseSeeder as DatabaseSeederV22;
use Modules\OpenAI\Database\Seeders\versions\v2_3_0\DatabaseSeeder as DatabaseSeederV23;

use Modules\OpenAI\Database\Seeders\versions\v2_5_0\DatabaseSeeder as DatabaseSeederV25;
use Modules\OpenAI\Database\Seeders\versions\v2_6_0\DatabaseSeeder as DatabaseSeederV26;

use Modules\OpenAI\Database\Seeders\versions\v2_7_0\DatabaseSeeder as DatabaseSeederV27;

use Modules\OpenAI\Database\Seeders\versions\v2_8_0\DatabaseSeeder as DatabaseSeederV28;
use Modules\OpenAI\Database\Seeders\versions\v2_9_0\DatabaseSeeder as DatabaseSeederV29;

use Modules\OpenAI\Database\Seeders\versions\v3_0_0\DatabaseSeeder as DatabaseSeederV30;

use Modules\OpenAI\Database\Seeders\versions\v3_1_0\DatabaseSeeder as DatabaseSeederV31;

use Modules\OpenAI\Database\Seeders\versions\v3_2_0\DatabaseSeeder as DatabaseSeederV32;

class OpenAIDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UseCaseCategoriesTableSeeder::class);
        $this->call(UseCasesTableSeeder::class);
        $this->call(UseCaseUseCaseCategoryTableSeeder::class);
        $this->call(OptionsTableSeeder::class);
        $this->call(OptionMetaTableSeeder::class);
        $this->call(ContentTypeTableSeeder::class);
        $this->call(ContentTypeMetaTableSeeder::class);

        $this->call(AdminMenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);

        $this->call(DatabaseSeederV12::class);

        $this->call(ChatCategoriesTableSeeder::class);

        $this->call(DatabaseSeederV14::class);

        $this->call(DatabaseSeederV15::class);
        
        $this->call(DatabaseSeederV16::class);
        $this->call(DatabaseSeederV17::class);

        $this->call(DatabaseSeederV18::class);
        $this->call(DatabaseSeederV20::class);

        $this->call(DatabaseSeederV22::class);

        $this->call(DatabaseSeederV23::class);

        $this->call(DatabaseSeederV25::class);
        $this->call(DatabaseSeederV26::class);

        $this->call(DatabaseSeederV27::class);

        $this->call(ChatBotsTableSeeder::class);
        
        $this->call(DatabaseSeederV28::class);
        $this->call(DatabaseSeederV29::class);
        $this->call(DatabaseSeederV30::class);

        $this->call(DatabaseSeederV31::class);

        $this->call(DatabaseSeederV32::class);
    }
}
