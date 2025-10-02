<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_9_0;

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
                'label' => 'Ai Chatbot',
                'link' => 'ai-chatbot',
                'params' => '{"permission":"Modules\\\\OpenAI\\\\Http\\\\Controllers\\\\Admin\\\\v2\\\\AiChatbotController@index","route_name":["admin.features.ai_chatbot.index", "admin.features.ai_chatbot.edit"]}',
                'is_default' => 1,
                'icon' => NULL,
                'parent' => 143,
                'sort' => 12,
                'class' => NULL,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0
            ],['link' => 'ai-chatbot']);

    }
}
