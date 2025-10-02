<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_8_0;

use Illuminate\Database\Seeder;

class ChatBotsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $ids = \DB::table('chat_bots')
        ->leftJoin('chat_bots_meta', function ($join) {
            $join->on('chat_bots.id', '=', 'chat_bots_meta.owner_id')
                ->where('chat_bots_meta.key', 'floating_image');
        })
        ->where('chat_bots.type', 'widgetChatbot')
        ->whereNull('chat_bots_meta.key')
        ->pluck('chat_bots.id');

        if ($ids->isNotEmpty()) {
            $data = array_map(function ($id) {
                return [
                    'owner_type' => 'Modules\\OpenAI\\Entities\\ChatBot',
                    'owner_id' => $id,
                    'type' => 'array',
                    'key' => 'floating_image',
                    'value' => '{"url":"public/uploads/20241008/2bcf18ce59a7b45c4bca7a5327333f41.png","is_delete":false}',
                ];
            }, $ids->toArray());
            
            \DB::table('chat_bots_meta')->insert($data);
        }
    }
}
