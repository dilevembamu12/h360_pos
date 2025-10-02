<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_5_0;

use Illuminate\Database\Seeder;

class SpeechToTextPreferenceTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('preferences')->upsert([
            [
                'category' => 'speechtotext',
                'field' => 'speechtotext_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on","visibility":true},{"type":"text","label":"Provider","name":"provider","value":"openai","visibility":true},{"type":"dropdown","label":"Models","name":"model","value":["whisper-1"],"visibility":true},{"type":"dropdown","label":"Word Filters","name":"word_filter","value":["Active","Inactive"],"visibility":true},{"type":"dropdown","label":"Languages","name":"language","value":["English","French","Arabic","Bulgarian","Byelorussian","Catalan","Estonian","Dutch","Russian","Spanish","Portuguese","Polish","German","Sweden"],"visibility":true},{"type":"dropdown","label":"Temperature","name":"temperature","value":[0,0.2,0.5,0.8,1],"default_value":0,"visibility":true}]',
            ]
        ], ['field']);
    }
}
