<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_3_0;

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
        // Code
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\CodeController@index', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\CodeController', 
            'controller_name' => 'CodeController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\CodeController@view', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\CodeController', 
            'controller_name' => 'CodeController',
            'method_name' => 'view',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\CodeController@delete', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\CodeController', 
            'controller_name' => 'CodeController',
            'method_name' => 'delete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        // Template 
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@documents', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'documents',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@favouriteDocuments', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'favouriteDocuments',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@template', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'template',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@getFormFiledByUsecase', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'getFormFiledByUsecase',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@getContent', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'getContent',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]); 
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@deleteContent', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'deleteContent',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@editContent', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'editContent',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController@updateContent', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Customer\\PrebuiltTemplateContentController', 
            'controller_name' => 'PrebuiltTemplateContentController',
            'method_name' => 'updateContent',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController@store', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\CodeController', 
            'controller_name' => 'CodeController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController@generate', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController', 
            'controller_name' => 'TemplateController',
            'method_name' => 'generate',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController@process', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\TemplateController', 
            'controller_name' => 'TemplateController',
            'method_name' => 'process',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
    }
}
