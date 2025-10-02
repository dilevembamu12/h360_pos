<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_2_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProviderManageMenusItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $providers  = DB::table('menu_items')->where('link', 'providers')->exists();

        if (!$providers) {
            DB::table('menu_items')->insert([
                [
                    'label' => 'Manage Providers',
                    'link' => "providers",
                    'params' => '{"permission":"modules\\\\openai\\\\http\\\\controllers\\\\admin\\\\openaicontroller@providers","route_name":["admin.features.providers","admin.features.provider_manage"]}',
                    'is_default' => 1,
                    'icon' => NULL,
                    'parent' => 31,
                    'sort' => 51,
                    'class' => NULL,
                    'menu' => 1,
                    'depth' => 1,
                    'is_custom_menu' => 0
                ]]);
        }
    }
}
