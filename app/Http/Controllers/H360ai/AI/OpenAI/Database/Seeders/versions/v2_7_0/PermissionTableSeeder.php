<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_7_0;

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
        // Image 
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController',
            'controller_name' => 'ImageController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController@destroy',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ImageController',
            'controller_name' => 'ImageController',
            'method_name' => 'destroy',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End Image

        // History
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\HistoryController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\HistoryController',
            'controller_name' => 'HistoryController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\HistoryController@show',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\HistoryController',
            'controller_name' => 'HistoryController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\HistoryController@destroy',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\HistoryController',
            'controller_name' => 'HistoryController',
            'method_name' => 'destroy',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End History

        // Ai Chat
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiChatController@show',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiChatController',
            'controller_name' => 'AiChatController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiChatController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiChatController',
            'controller_name' => 'AiChatController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        //End Ai Chat

        // Ai Doc Chat
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDocChatController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDocChatController',
            'controller_name' => 'AiDocChatController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDocChatController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDocChatController',
            'controller_name' => 'AiDocChatController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDocChatController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\AiDocChatController',
            'controller_name' => 'AiDocChatController',
            'method_name' => 'delete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End Doc Chat

        // Ai Doc Chat Asking 
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\DocChatAskController@askQuestion',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\DocChatAskController',
            'controller_name' => 'DocChatAskController',
            'method_name' => 'askQuestion',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End Doc Chat Asking

        // Feature Preference 
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController@preference',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController',
            'controller_name' => 'FeatureManagerController',
            'method_name' => 'preference',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController@addiontalOptions',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController',
            'controller_name' => 'FeatureManagerController',
            'method_name' => 'addiontalOptions',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End Feature Preference

        // Start Chatbot Widget
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController',
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController',
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@show', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController', 
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@update',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController', 
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'update',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@delete', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController', 
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'delete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@destroyImage', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController', 
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'destroyImage',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController@dashboard', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotWidgetController', 
            'controller_name' => 'ChatBotWidgetController',
            'method_name' => 'dashboard',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        // End Chatbot Widget

        // Strat Chatbot Training
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotTrainingController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController',
            'controller_name' => 'ChatBotTrainingController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController',
            'controller_name' => 'ChatBotTrainingController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController@train',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController',
            'controller_name' => 'ChatBotTrainingController',
            'method_name' => 'train',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController@destroy',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController',
            'controller_name' => 'ChatBotTrainingController',
            'method_name' => 'destroy',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController@fetchUrl', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController', 
            'controller_name' => 'ChatBotTrainingController',
            'method_name' => 'fetchUrl',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController@csv', 
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotTrainingController', 
            'controller_name' => 'ChatBotTrainingController',
            'method_name' => 'csv',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        // Start Feature Manager
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController@providers',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController',
            'controller_name' => 'FeatureManagerController',
            'method_name' => 'providers',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController@models',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeatureManagerController',
            'controller_name' => 'FeatureManagerController',
            'method_name' => 'models',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End Feature Manager

        // Start Feature Preference
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeaturePreferenceController@featureOptions',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\FeaturePreferenceController',
            'controller_name' => 'FeaturePreferenceController',
            'method_name' => 'featureOptions',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);
        // End Feature Preference

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserTestConversationController@store',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotUserTestConversationController',
            'controller_name' => 'ChatBotUserTestConversationController',
            'method_name' => 'store',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserTestConversationController@show',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotUserTestConversationController',
            'controller_name' => 'ChatBotUserTestConversationController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        // End Chatbot User Test Conversation

        // Start Chatbot User Conversation
        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserConversationController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotUserConversationController',
            'controller_name' => 'ChatBotUserConversationController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserConversationController@show',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotUserConversationController',
            'controller_name' => 'ChatBotUserConversationController',
            'method_name' => 'show',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\OpenAI\Http\Controllers\Api\v2\User\ChatBotUserConversationController@delete',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\ChatBotUserConversationController',
            'controller_name' => 'ChatBotUserConversationController',
            'method_name' => 'delete',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        $parentId = Permission::firstOrCreate([
            'name' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\UserAccessController@index',
            'controller_path' => 'Modules\\OpenAI\\Http\\Controllers\\Api\\v2\\User\\UserAccessController',
            'controller_name' => 'UserAccessController',
            'method_name' => 'index',
        ]);
        DB::table('permission_roles')->insert([
            'permission_id' =>$parentId->id,
            'role_id' => 2,
        ]);

        // End Chatbot User Conversation
    }
}
