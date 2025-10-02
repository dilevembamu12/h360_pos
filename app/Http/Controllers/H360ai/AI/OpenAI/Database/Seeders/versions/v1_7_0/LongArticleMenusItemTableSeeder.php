<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LongArticleMenusItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('menu_items')->updateOrInsert(
            [
                'label' => 'Long Article',
                'link' => 'articles',
                'params' => '{"permission":"Modules\\\\OpenAI\\\\Http\\\\Controllers\\\\Admin\\\\LongArticleController@index","route_name":["admin.long_article.index", "admin.long_article.edit"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 143,
                'sort' => 12,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0
            ],['link' => 'articles']);

    }
}
