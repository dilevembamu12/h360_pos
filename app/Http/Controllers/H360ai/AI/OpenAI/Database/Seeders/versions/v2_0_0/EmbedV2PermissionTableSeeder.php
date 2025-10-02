<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class EmbedV2PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // EmbedController
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController@index', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController', 
            'controller_name' => 'EmbedController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController@store', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController', 
            'controller_name' => 'EmbedController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController@show', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController', 
            'controller_name' => 'EmbedController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);


        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController@delete', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController', 
            'controller_name' => 'EmbedController',
            'method_name' => 'delete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController@summarize', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController', 
            'controller_name' => 'EmbedController',
            'method_name' => 'summarize',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController@askQuestion', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\EmbedController', 
            'controller_name' => 'EmbedController',
            'method_name' => 'askQuestion',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
       
    }
}
