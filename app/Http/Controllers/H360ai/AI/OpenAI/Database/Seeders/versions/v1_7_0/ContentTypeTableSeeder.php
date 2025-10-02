<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

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
        $documentId = ContentType::where('slug', 'document')->value('id');
        if ($documentId){
            ContentTypeMeta::where('name', 'document')->where('key', 'tone')->upsert([
                'content_type_id' => $documentId,
                'name' => 'document',
                'key' => 'tone',
                'value' => '["Casual", "Funny", "Bold", "Feminine", "Professional", "Friendly", "Dramatic", "Sarcastic", "Excited", "Empathetic", "Playful"]'
            ], ['key', 'name']);
        }

        $metaUpdates = [
            'variant' => ['openai_variant', '["1"]'],
            'resulation' => ['openai_resulation', '["1024x1024", "1024x1792", "1792x1024"]'],
            'artStyle' => ['openai_artStyle', '["Normal","Cartoon art","3D Render","Pixel art","Isometric","Vendor art","Line art","Watercolor art","Anime art"]'],
            'lightingStyle' => ['openai_lightingStyle', '["Normal","Studio","Warm","Cold","Ambient","Neon","Foggy"]'],
        ];
        
        foreach ($metaUpdates as $metaKey => $metaData) {
            $metaExists = ContentTypeMeta::where('name', 'image_maker')->where('key', $metaKey)->exists();
        
            if ($metaExists) {
                ContentTypeMeta::where('name', 'image_maker')->where('key', $metaKey)->update([
                    'key' => $metaData[0],
                    'value' => $metaData[1],
                ]);
            }
        }
        
        $id = ContentType::where('slug', 'image_maker')->value('id');

        if ($id) {

            ContentTypeMeta::where('name', 'image_maker')->where('key', 'imageCreateFrom')->upsert([
                'content_type_id' => $id,
                'name' => 'image_maker',
                'key' => 'imageCreateFrom',
                'value' => '{"openai" : "1","stable_diffusion" : "1"}'
            ], ['key', 'name']);


            $metaInserts = [
                ['stable_diffusion_variant', '["1", "2", "3"]'],
                ['stable_diffusion_resulation', '["1536x640","1152x896","1344x768","1024x1024","768x1344", "640x1536", "832x1216", "896x1152"]'],
                ['stable_diffusion_artStyle', '["Normal","Cartoon art","3D Render","Pixel art","Isometric","Vendor art","Line art","Watercolor art","Anime art"]'],
                ['stable_diffusion_lightingStyle', '["Normal","Studio","Warm","Cold","Ambient","Neon","Foggy"]'],
            ];
        
            foreach ($metaInserts as $metaData) {
                $metaExists = ContentTypeMeta::where('key', $metaData[0])->exists();
        
                if (!$metaExists) {
                    ContentTypeMeta::insert([
                        'content_type_id' => $id,
                        'name' => 'image_maker',
                        'key' => $metaData[0],
                        'value' => $metaData[1],
                    ]);
                }
            }
        }
    
    }
}
