<?php

namespace Modules\OpenAI\Database\Seeders\versions\v1_7_0;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use DB;

class LongArticlePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@index', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController', 
            'controller_name' => 'LongArticleController',
            'method_name' => 'index',
        ]);

        Permission::firstOrCreate([
            'name' =>  'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@edit', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController', 
            'controller_name' => 'LongArticleController',
            'method_name' => 'edit',

        ]);
        Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@update', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController', 
            'controller_name' => 'LongArticleController',
            'method_name' => 'update',

        ]);
        Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@destroy', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController', 
            'controller_name' => 'LongArticleController',
            'method_name' => 'destroy',

        ]);

        Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@csv', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController', 
            'controller_name' => 'LongArticleController',
            'method_name' => 'csv',

        ]);
        Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController@pdf', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Admin\\LongArticleController', 
            'controller_name' => 'LongArticleController',
            'method_name' => 'pdf',

        ]);

        // User
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'index',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@create',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'create',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@view',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'view',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>  $parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@edit',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'edit',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@update',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'update',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' => $parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'delete',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@generateTitles',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'generateTitles',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@generateOutlines',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'generateOutlines',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@initArticle',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'initArticle',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@generateArticle',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'generateArticle',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@displayTitleData',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'displayTitleData',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@displayOutlineData',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'displayOutlineData',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController@displayArticleBlogData',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\LongArticleController',
            'controller_name' => 'LongArticleController',
            'method_name' => 'displayArticleBlogData',
        ]);

        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
    }
}
