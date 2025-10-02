<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

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
            ContentTypeTableSeeder::class,
            VoiceTableSeeder::class,
            ChatBotV2PermissionTableSeeder::class,
            ChatV2PermissionTableSeeder::class,
            PermissionTableSeeder::class,
            EmbedV2PermissionTableSeeder::class,
            DocChatV2PermissionTableSeeder::class,
        ]);
    }
}
