<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $dbPreference = \DB::table('voices')->where('name', 'Lyra Faye')->first();

        if (!$dbPreference) {
            $voiceId = DB::table('voices')->insertGetId([
                'name' => 'Lyra Faye',
                'voice_name' => 'en-US-Standard-E',
                'language_code' => 'en-US',
                'gender' => 'Female',
                'file_name' => '20240312/f2166de7860687ef5d931b6beb3d427c.mp3',
                'status' => 'Active',
            ]);
            $fileId = DB::table('files')->insertGetId([
                'params' => '{"size":19.115234375,"type":"png"}',
                'object_type' => 'png',
                'object_id' => NULL,
                'uploaded_by' => 1,
                'file_name' => '20240312/a0596457a3be0d35f9e6af4777854510.png',
                'file_size' => 19.12,
                'original_file_name' => 'an-extremely-detailed-photo-portrait-of-beautiful-caucasian-female-in-corporate-clothes-brown-curly-872876670.png',
            ]);
            DB::table('object_files')->insert([
                'object_type' => 'voices',
                'object_id' => $voiceId,
                'file_id' => $fileId,
            ]);
        }
        
        $dbPreference = \DB::table('voices')->where('name', 'Aria Serene')->first();
        if(!$dbPreference) {
            $voiceId = DB::table('voices')->insertGetId([
                'name' => 'Aria Serene',
                'voice_name' => 'en-US-Standard-F',
                'language_code' => 'en-US',
                'gender' => 'Female',
                'file_name' => '20240312/ce38c617d0c2c2d0a35e702850ec24cc.mp3',
                'status' => 'Active',
            ]);
            $fileId = DB::table('files')->insertGetId([
                'params' => '{"size":16.6650390625,"type":"png"}',
                'object_type' => 'png',
                'object_id' => NULL,
                'uploaded_by' => 1,
                'file_name' => '20240312/270a87991c1f41ac848f9a3f7d1618ea.png',
                'file_size' => 16.67,
                'original_file_name' => 'black-background-photography-nice-beautiful-french-embarrassed-young-lady-poses-for-a-photo-lustfu-298195612 (1).png',
            ]);
            DB::table('object_files')->insert([
                'object_type' => 'voices',
                'object_id' => $voiceId,
                'file_id' => $fileId,
            ]);
        }
        
        $dbPreference = \DB::table('voices')->where('name', 'Nova Celeste')->first();
        if(!$dbPreference) {
            $voiceId = DB::table('voices')->insertGetId([
                'name' => 'Nova Celeste',
                'voice_name' => 'en-US-Standard-G',
                'language_code' => 'en-US',
                'gender' => 'Female',
                'file_name' => '20240312/058808c764fbdf424cd39759f34040aa.mp3',
                'status' => 'Active',
            ]);
            $fileId = DB::table('files')->insertGetId([
                'params' => '{"size":19.3017578125,"type":"png"}',
                'object_type' => 'png',
                'object_id' => NULL,
                'uploaded_by' => 1,
                'file_name' => '20240312/a0bdd792bd435cfc28cf2b53eb46bead.png',
                'file_size' => 19.3,
                'original_file_name' => 'detailed-photograph-of-a-serious-looking-young-gentlewoman-looking-straight-into-the-camera-standin-65892122.png',
            ]);
            DB::table('object_files')->insert([
                'object_type' => 'voices',
                'object_id' => $voiceId,
                'file_id' => $fileId,
            ]);
        }

        $dbPreference = \DB::table('voices')->where('name', 'Thorne Archer')->first();
        if(!$dbPreference) {
            $voiceId = DB::table('voices')->insertGetId([
                'name' => 'Thorne Archer',
                'voice_name' => 'en-US-Standard-I',
                'language_code' => 'en-US',
                'gender' => 'Male',
                'file_name' => '20240312/623e467fd605d95f913ace2f7540948b.mp3',
                'status' => 'Active',
            ]);
            $fileId = DB::table('files')->insertGetId([
                'params' => '{"size":5.0048828125,"type":"jpeg"}',
                'object_type' => 'jpeg',
                'object_id' => NULL,
                'uploaded_by' => 1,
                'file_name' => '20240312/29114203103557051d3b3dc5d3d998cb.jpeg',
                'file_size' => 5.0,
                'original_file_name' => 'main-bhi-sabji-banaa-rahi-hoon - 836036885.jpeg',
            ]);
            DB::table('object_files')->insert([
                'object_type' => 'voices',
                'object_id' => $voiceId,
                'file_id' => $fileId,
            ]);
        }

        $dbPreference = \DB::table('voices')->where('name', 'Atlas Steele')->first();
        if(!$dbPreference) {
            $voiceId = DB::table('voices')->insertGetId([
                'name' => 'Atlas Steele',
                'voice_name' => 'en-US-Standard-J',
                'language_code' => 'en-US',
                'gender' => 'Male',
                'file_name' => '20240312/a7c684a488a5f802e79f1d0a7b7223b3.mp3',
                'status' => 'Active',
            ]);
            $fileId = DB::table('files')->insertGetId([
                'params' => '{"size":11.755859375,"type":"png"}',
                'object_type' => 'png',
                'object_id' => NULL,
                'uploaded_by' => 1,
                'file_name' => '20240312/3436f90c8e8e5264f4074a7f4c3ebfd0.png',
                'file_size' => 11.76,
                'original_file_name' => 'portrait-from-the-waist-up-a-pleasant-looking-businessman-a-confident-look-in-a-business-suit-loo-318967739.png',
            ]);
            DB::table('object_files')->insert([
                'object_type' => 'voices',
                'object_id' => $voiceId,
                'file_id' => $fileId,
            ]);
        }

       
    }
}
