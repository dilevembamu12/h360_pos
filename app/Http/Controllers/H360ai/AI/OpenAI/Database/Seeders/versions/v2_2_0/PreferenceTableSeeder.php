<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_2_0;

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
                ['field' => 'longarticle_openai'],
                [
                    'category' => 'longarticle',
                    'field' => 'longarticle_openai',
                    'value' => '[{"type":"checkbox","label":"Provider State","name":"status","value":"on"},{"type":"text","label":"Provider","name":"provider","value":"openai"},{"type":"dropdown","label":"Models","name":"model","value":["gpt-4","gpt-3.5-turbo","gpt-4o"]},{"type":"dropdown","label":"Tones","name":"tone","value":["Normal","Formal","Casual","Professional","Serious","Friendly","Playful","Authoritative","Empathetic","Persuasive","Optimistic","Sarcastic","Informative","Inspiring","Humble","Nostalgic","Dramatic"]},{"type":"dropdown","label":"Languages","name":"language","value":["English","French","Arabic","Byelorussian","Bulgarian","Catalan","Estonian","Dutch"]},{"type":"dropdown","label":"Frequency Penalty","name":"frequency_penalty","value":[0,0.5,1,1.5,2],"default_value":0},{"type":"dropdown","label":"Presence Penalty","name":"presence_penalty","value":[0,0.5,1,1.5,2],"default_value":0},{"type":"dropdown","label":"Temperature","name":"temperature","value":[0,0.5,1,1.5,2],"default_value":1},{"type":"dropdown","label":"Top P","name":"top_p","value":[0,0.25,0.5,0.75,1],"default_value":1}]'
                ]
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
        }
    }
}
