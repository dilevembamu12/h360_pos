<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_3_0;

use Illuminate\Database\Seeder;
use App\Models\Preference;
use Exception, Log, DB;

class PreferenceTableSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();

        try {
            Preference::updateOrCreate(
                ['field' => 'aichat_openai'],
                [
                    'category' => 'aichat',
                    'field' => 'aichat_openai',
                    'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on","visibility":true},{"type":"text","label":"Provider","name":"provider","value":"openai","visibility":true},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"],"visibility":true},{"type":"dropdown","label":"Tones","name":"tone","value":["Normal","Formal","Casual","Professional","Serious","Friendly","Playful","Authoritative","Empathetic","Persuasive","Optimistic","Sarcastic","Informative","Inspiring","Humble","Nostalgic","Dramatic"],"visibility":true},{"type":"dropdown","label":"Languages","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch"],"visibility":true},{"type":"integer","label":"Max Tokens","name":"max_tokens","value":"2048","visibility":true}]'
                ]
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
        \DB::table('preferences')->upsert([
            [
                'category' => 'templatecontent',
                'field' => 'templatecontent_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"dropdown","label":"Language","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch","Russian","Spanish","Portuguese","Polish","German","Sweden"]},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"]},{"type":"dropdown","label":"Tone","name":"tone","value":["Casual","Funny","Bold","Feminine","Professional","Friendly","Dramatic","Playful","Excited","Sarcastic","Empathetic"]},{"type":"dropdown","label":"Number Of Variant","name":"variant","value":[1,2,3]},{"type":"dropdown","label":"Creativity Level","name":"creativity_level","value":["Optimal","Low","Medium","High"]},{"type":"integer","label":"Max Tokens","name":"max_tokens","value":"4096","visibility":true}]',
            ],
            [
                'category' => 'code',
                'field' => 'code_openai',
                'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"dropdown","label":"Language","name":"language","value":["PHP","Java","Rubby","Python","C#","Go","Kotlin","HTML","Javascript","TypeScript","SQL","NoSQL"]},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"]},{"type":"dropdown","label":"Code Level","name":"code_level","value":["Noob","Moderate","High"]},{"type":"integer","label":"Max Tokens","name":"max_tokens","value":"4096","visibility":true}]',
            ],
        ], ['field']);
    }
}
