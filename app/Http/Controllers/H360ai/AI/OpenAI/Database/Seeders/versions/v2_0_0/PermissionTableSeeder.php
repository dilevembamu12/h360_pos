<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
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
        Model::unguard();

        // Image Conversation
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\ImageController@converstaionList', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\ImageController', 
            'controller_name' => 'ImageController',
            'method_name' => 'converstaionList',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\ImageController@converstaionView', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\ImageController', 
            'controller_name' => 'ImageController',
            'method_name' => 'converstaionView',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\ImageController@conversationDelete', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\ImageController', 
            'controller_name' => 'ImageController',
            'method_name' => 'conversationDelete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        // OpenAI Preferecne 
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\OpenAIPreferenceController@aiProviders', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\OpenAIPreferenceController', 
            'controller_name' => 'OpenAIPreferenceController',
            'method_name' => 'aiProviders',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\OpenAIPreferenceController@conversationData', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\V1\\User\\OpenAIPreferenceController', 
            'controller_name' => 'OpenAIPreferenceController',
            'method_name' => 'conversationData',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
    }
}
