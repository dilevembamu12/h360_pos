<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_5_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use DB;

class SpeechToTextPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController@template',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController',
            'controller_name' => 'SpeechToTextController',
            'method_name' => 'template',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController',
            'controller_name' => 'SpeechToTextController',
            'method_name' => 'index',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController@edit',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController',
            'controller_name' => 'SpeechToTextController',
            'method_name' => 'edit',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController@update',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController',
            'controller_name' => 'SpeechToTextController',
            'method_name' => 'update',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\SpeechToTextController',
            'controller_name' => 'SpeechToTextController',
            'method_name' => 'delete',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\SpeechToTextController@generate',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\SpeechToTextController',
            'controller_name' => 'SpeechToTextController',
            'method_name' => 'generate',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);
    }
}
