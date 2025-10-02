<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_8_0;

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
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDetectorController@generate',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\v2\\User\\AiDetectorController',
            'controller_name' => 'AiDetectorController',
            'method_name' => 'generate',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\v2\\AiDetectorController@template',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\v2\\AiDetectorController',
            'controller_name' => 'AiDetectorController',
            'method_name' => 'template',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);
    }
}
