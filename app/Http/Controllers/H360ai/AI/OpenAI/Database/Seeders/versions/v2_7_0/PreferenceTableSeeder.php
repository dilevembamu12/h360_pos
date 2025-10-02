<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_7_0;

use Illuminate\Database\Seeder;

class PreferenceTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('preferences')->upsert([
            [
                'category' => 'aiembedding',
                'field' => 'aiembedding_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"text","label":"Provider","name":"provider","value":"openai"},{"type":"dropdown","label":"Models","name":"model","value":["text-embedding-ada-002","text-embedding-3-large","text-embedding-3-small"]}]'
            ],
            [
                'category' => 'chatbot',
                'field' => 'chatbot_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"text","label":"Provider","name":"provider","value":"openai"},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4o-mini","gpt-4o","gpt-4","gpt-3.5-turbo"]}]',
            ],
            [
                'category' => 'aidocchat',
                'field' => 'aidocchat_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"text","label":"Provider","name":"provider","value":"openai"},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4o","gpt-4-1106-preview","gpt-4-0125-preview","gpt-4","gpt-3.5-turbo","gpt-3.5-turbo-16k","gpt-3.5-turbo-1106","gpt-3.5-turbo-0125"],"required":true},{"type":"dropdown","label":"Temperature","name":"temperature","value":[0,0.5,1,1.5,2],"default_value":1},{"type":"number","label":"Max Tokens","name":"max_tokens","min":1,"max":4096,"value":"2048","visibility":true,"required":true}]',
            ],
            [
                'category' => 'aichat',
                'field' => 'aichat_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on","visibility":true},{"type":"text","label":"Provider","name":"provider","value":"openai","visibility":true},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4o","gpt-4","gpt-3.5-turbo"],"visibility":true,"required":true},{"type":"dropdown","label":"Tones","name":"tone","value":["Normal","Formal","Casual","Professional","Serious","Friendly","Playful","Authoritative","Empathetic","Persuasive","Optimistic","Sarcastic","Informative","Inspiring","Humble","Nostalgic","Dramatic"],"visibility":true},{"type":"dropdown","label":"Languages","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch"],"visibility":true},{"type":"number","label":"Max Tokens","name":"max_tokens","min":1,"max":4096,"value":"2048","visibility":true,"required":true}]',
            ],
            [
                'category' => 'aichat',
                'field' => 'aichat_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on","visibility":true},{"type":"text","label":"Provider","name":"provider","value":"openai","visibility":true},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4o","gpt-4","gpt-3.5-turbo"],"visibility":true,"required":true},{"type":"dropdown","label":"Tones","name":"tone","value":["Normal","Formal","Casual","Professional","Serious","Friendly","Playful","Authoritative","Empathetic","Persuasive","Optimistic","Sarcastic","Informative","Inspiring","Humble","Nostalgic","Dramatic"],"visibility":true},{"type":"dropdown","label":"Languages","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch"],"visibility":true},{"type":"number","label":"Max Tokens","name":"max_tokens","min":1,"max":4096,"value":"2048","visibility":true,"required":true}]'
            ],
            [
                'category' => 'code',
                'field' => 'code_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"dropdown","label":"Language","name":"language","value":["PHP","Java","Rubby","Python","C#","Go","Kotlin","HTML","Javascript","TypeScript","SQL","NoSQL"]},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"]},{"type":"dropdown","label":"Code Level","name":"code_level","value":["Noob","Moderate","High"]},{"type":"number","label":"Max Tokens","name":"max_tokens","min":1,"max":4096,"value":"2048","visibility":true,"required":true}]'
            ],
            [
                'category' => 'longarticle',
                'field' => 'longarticle_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"text","label":"Provider","name":"provider","value":"openai"},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"]},{"type":"dropdown","label":"Tones","name":"tone","value":["Normal","Formal","Casual","Professional","Serious","Friendly","Playful","Authoritative","Empathetic","Persuasive","Optimistic","Sarcastic","Informative","Inspiring","Humble","Nostalgic","Dramatic"]},{"type":"dropdown","label":"Languages","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch"]},{"type":"dropdown","label":"Frequency Penalty","name":"frequency_penalty","value":[0,0.5,1,1.5,2],"default_value":0},{"type":"dropdown","label":"Presence Penalty","name":"presence_penalty","value":[0,0.5,1,1.5,2],"default_value":0},{"type":"dropdown","label":"Temperature","name":"temperature","value":[0,0.5,1,1.5,2],"default_value":1},{"type":"dropdown","label":"Top P","name":"top_p","value":[0,0.25,0.5,0.75,1],"default_value":1},{"type":"number","label":"Max Tokens","name":"max_tokens","min":1,"max":4096,"value":"2048","visibility":true,"required":true}]'
            ],
            [
                'category' => 'templatecontent',
                'field' => 'templatecontent_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"dropdown","label":"Language","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch","Russian","Spanish","Portuguese","Polish","German","Sweden"]},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"]},{"type":"dropdown","label":"Tone","name":"tone","value":["Casual","Funny","Bold","Feminine","Professional","Friendly","Dramatic","Playful","Excited","Sarcastic","Empathetic"]},{"type":"dropdown","label":"Number Of Variant","name":"variant","value":[1,2,3]},{"type":"dropdown","label":"Creativity Level","name":"creativity_level","value":["Optimal","Low","Medium","High"]},{"type":"number","label":"Max Tokens","name":"max_tokens","min":1,"max":4096,"value":"2048","visibility":true,"required":true}]'
            ],
            [
                'category' => 'imagemaker',
                'field' => 'imagemaker_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on","visibility":false},{"type":"text","label":"Provider","name":"provider","value":"openai","visibility":false},{"type":"dropdown","label":"Models","name":"model","value":["dall-e-2","dall-e-3"],"default_value":"dall-e-2","visibility":true,"required":true},{"type":"dropdown","label":"Variant","name":"variant","value":[1,2,3,4,5,6,7,8,9,10],"default_value":1,"visibility":true,"required":true},{"type":"dropdown","label":"Quality","name":"quality","value":["standard","hd"],"default_value":"standard","visibility":true,"required":true},{"type":"dropdown","label":"Size","name":"size","value":["256x256","512x512","1024x1024","1792x1024","1024x1792"],"visibility":true,"required":true},{"type":"dropdown","label":"Art Style","name":"art_style","value":["Normal","3D Model","Analog Film","Anime","Cinematic","Comic Book","Digital Art","Enhance","Fantacy Art","Icometric","Line Art","Low Poly","Modeling Compound","Neon Punk","Origami","Photographic","Pixel Art","Tile Texture","Water Color"],"default":"Normal","visibility":true,"required":true},{"type":"dropdown","label":"Light Effect","name":"light_effect","value":["Normal","Studio","Warm","Cold","Ambient","Neon","Foggy"],"default":"Normal","visibility":true,"required":true}]'
            ]
        ], ['value']);

        $userPermission =  \DB::table('preferences')->where('field', 'user_permission')->first();

        if ($userPermission) {
            $value = json_decode($userPermission->value, true) + ['hide_aichatbot' => '0'];
            \DB::table('preferences')->where('field', 'user_permission')->update(['value' => $value]);
        }
    }
}
