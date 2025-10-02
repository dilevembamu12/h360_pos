<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_2_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('menu_items')->updateOrInsert(
        [
            'label' => 'Image To Video',
            'link' => 'image-to-video',
            'params' => '{"permission":"Modules\\\\OpenAI\\\\Http\\\\Controllers\\\\Admin\\\\v2\\\\ImageToVideoController@index","route_name":["admin.features.image-to-video.index"]}',
            'is_default' => 1,
            'icon' => NULL,
            'parent' => 143,
            'sort' => 12,
            'class' => NULL,
            'menu' => 1,
            'depth' => 1,
            'is_custom_menu' => 0
        ],['link' => 'image-to-video']);
    }
}
