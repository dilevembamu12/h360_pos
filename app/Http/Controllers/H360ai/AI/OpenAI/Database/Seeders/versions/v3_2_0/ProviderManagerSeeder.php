<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_2_0;

use Illuminate\Database\Seeder;

class ProviderManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('preferences')->upsert([
            [
                'category' => 'videomaker',
                'field' => 'videomaker_stabilityai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on","visibility":true},{"type":"text","label":"Provider","name":"provider","value":"stabilityai","visibility":false},{"type":"number","label":"Motion Intensity","name":"motion_intensity","min":0,"max":255,"value":"160","tooltip":"Lower values generally result in less motion in the output video, while higher values generally result in more motion. The range is 0 ~ 255","visibility":true},{"type":"number","label":"Seed","name":"seed","value":"0","min":0,"max":4294967294,"tooltip":"A specific value from 0 to 4294967294 that is used to guide the randomness of the generation.","visibility":true},{"type":"slider","label":"Image Strength","name":"image_strength","min":0,"max":10,"value":"6","required":true,"tooltip":"A specific value from 0 to 10 to express how strongly the video sticks to the original image.","visibility":true}]',
            ]
        ], ['field']);

    }
}
