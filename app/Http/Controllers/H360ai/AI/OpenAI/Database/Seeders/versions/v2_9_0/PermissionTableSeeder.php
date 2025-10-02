<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_9_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use DB;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController',
            'controller_name' => 'AiChatbotController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 1,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController@edit',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController',
            'controller_name' => 'AiChatbotController',
            'method_name' => 'edit',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 1,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController@update',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController',
            'controller_name' => 'AiChatbotController',
            'method_name' => 'update',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 1,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController',
            'controller_name' => 'AiChatbotController',
            'method_name' => 'delete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 1,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController@pdf',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController',
            'controller_name' => 'AiChatbotController',
            'method_name' => 'pdf',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 1,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController@csv',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\v2\\AiChatbotController',
            'controller_name' => 'AiChatbotController',
            'method_name' => 'csv',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 1,
        ]);
    }
}
