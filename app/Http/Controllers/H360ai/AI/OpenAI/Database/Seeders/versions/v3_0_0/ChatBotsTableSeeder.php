<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_0_0;

use Illuminate\Database\Seeder;
use DB;

class ChatBotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categoryId =  DB::table('chat_categories')->where('slug', 'others')->value('id');

        // Upsert for the chat_bots table
        DB::table('chat_bots')->upsert([
            'chat_category_id' => $categoryId,
            'user_id' => 1,
            'name' => 'Dora',
            'code' => 'dora-101',
            'message' => 'Hello, I\'m Dora, an Image Expert. How may I assist you today?',
            'role' => 'Image Expert',
            'promt' => 'Imagine being an Image Expertise professional who excels at reading image details. Your role involves analyzing and extracting key information from images. Consider the composition, resolution, dimensions, and any unique features of the image. Your expertise is crucial for maximizing the use of visual content.',
            'status' => 'Active',
            'type' => 'vision',
            'is_default' => 0,
        ], ['code']);

        // Retrieve the botId after upsert
        $botId = DB::table('chat_bots')->where('code', 'dora-101')->value('id');

        // Upsert for the files table
        DB::table('files')->upsert([
            'params' => '{"size":38.671875,"type":"png"}',
            'object_type' => 'png',
            'uploaded_by' => 1,
            'file_name' => '20240926\\3b301af11bcfa99ae97059eefb43f480.png',
            'file_size' => 38.67,
            'original_file_name' => 'robo image.png',
        ], ['file_name']);

        // Retrieve the fileId after upsert
        $fileId = DB::table('files')->where('file_name', '20240926\\3b301af11bcfa99ae97059eefb43f480.png')->value('id');

        // Upsert for the object_files table
        DB::table('object_files')->upsert([
            'object_type' => 'chat_bots',
            'object_id' => $botId,
            'file_id' => $fileId,
        ], ['object_type', 'object_id', 'file_id']);

    }
}
