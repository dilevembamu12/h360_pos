<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class ChatV2PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // ChatController
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController@index', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController', 
            'controller_name' => 'ChatController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController@show', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController', 
            'controller_name' => 'ChatController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController@store', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController', 
            'controller_name' => 'ChatController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController@destroy', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatController', 
            'controller_name' => 'ChatController',
            'method_name' => 'destroy',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
    }
}
