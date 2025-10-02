<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_0_0;

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
        // Vision
        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\VisionController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\VisionController',
            'controller_name' => 'VisionController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);
    }
}
