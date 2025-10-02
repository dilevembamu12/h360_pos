<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

use Illuminate\Database\Seeder;
use Modules\OpenAI\Entities\{
    ContentTypeMeta,
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

        $meta = ContentTypeMeta::where('name', 'image_maker')->where('key', 'clipdrop_apis_engine')->first();
        $updatedMeta = ContentTypeMeta::where('name', 'image_maker')->where('key', 'clipdrop_services')->first();
        
        if ($meta && !$updatedMeta) {
            $meta->update(
                [
                    'key' => 'clipdrop_services'
                ]
    
            );
        }
        
    }
}
