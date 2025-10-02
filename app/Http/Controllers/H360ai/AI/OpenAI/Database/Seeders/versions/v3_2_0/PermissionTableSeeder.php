<?php

namespace Modules\OpenAI\Database\Seeders\versions\v3_2_0;

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
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v3\\User\\FeatureManagerController@providers',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v3\\User\\FeatureManagerController',
            'controller_name' => 'FeatureManagerController',
            'method_name' => 'providers',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\UseCaseController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\UseCaseController',
            'controller_name' => 'UseCaseController',
            'method_name' => 'index',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController',
            'controller_name' => 'CodeController',
            'method_name' => 'index',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController@show',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController',
            'controller_name' => 'CodeController',
            'method_name' => 'show',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController',
            'controller_name' => 'CodeController',
            'method_name' => 'delete',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController',
            'controller_name' => 'TemplateController',
            'method_name' => 'index',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController@show',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController',
            'controller_name' => 'TemplateController',
            'method_name' => 'show',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController',
            'controller_name' => 'TemplateController',
            'method_name' => 'delete',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController',
            'controller_name' => 'ImageController',
            'method_name' => 'index',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        $parentId = Permission::insertGetId([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController@toggleFavorite',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController',
            'controller_name' => 'ImageController',
            'method_name' => 'toggleFavorite',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId,
            'role_id' => 2,
        ]);

        // Image To Video
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageToVideoController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageToVideoController',
            'controller_name' => 'ImageToVideoController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' => $parentId->id,
            'role_id' => 2,
        ]);
    }
}
