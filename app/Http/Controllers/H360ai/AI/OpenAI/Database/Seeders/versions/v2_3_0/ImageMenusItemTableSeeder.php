<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_3_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageMenusItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menu_items')
        ->where('link', 'image/list')
        ->update([
            'link' => 'images', 
            'params' => '{"permission":"modules\\\\openai\\\\http\\\\controllers\\\\admin\\\\v2\\\\ImageController@index","route_name":["admin.features.admin.image.index"]}',
        ]);
    }
}
