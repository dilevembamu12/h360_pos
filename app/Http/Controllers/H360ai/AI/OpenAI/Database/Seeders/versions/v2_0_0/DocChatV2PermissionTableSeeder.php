<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_0_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class DocChatV2PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\DocChatController@conversation', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\DocChatController', 
            'controller_name' => 'DocChatController',
            'method_name' => 'conversation',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
    }
}
