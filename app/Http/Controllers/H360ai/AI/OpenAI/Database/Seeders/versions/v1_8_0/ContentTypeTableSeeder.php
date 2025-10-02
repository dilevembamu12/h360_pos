<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_8_0;

use Illuminate\Database\Seeder;
use Modules\OpenAI\Entities\{
    ContentTypeMeta,
    ContentType
};

class ContentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $id = ContentType::where('slug', 'image_maker')->value('id');

        if ($id) {

            ContentTypeMeta::upsert([
                [
                    'content_type_id' => $id,
                    'name' => 'image_maker',
                    'key' => 'clipdrop_apis_engine',
                    'value' => '["text-to-image","sketch-to-image","replace-background","remove-background","remove-text","reimagine"]'
                ],
                [
                    'content_type_id' => $id,
                    'name' => 'image_maker',
                    'key' => 'clipdrop_artStyle',
                    'value' => '["Normal","Cartoon art","3D Render","Pixel art","Isometric","Vendor art","Line art","Watercolor art","Anime art"]'
                ],
                [
                    'content_type_id' => $id,
                    'name' => 'image_maker',
                    'key' => 'clipdrop_lightingStyle',
                    'value' => '["Normal","Studio","Warm","Cold","Ambient","Neon","Foggy"]'
                ]
    
            ], ['content_type_id', 'name', 'key']);

        }
    }
}
