<?php

namespace Modules\OpenAI\Database\Seeders\versions\v2_7_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeaturePreferenceMenusItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \DB::table('menu_items')->updateOrInsert(
            [
                'link' => 'features'  // Condition to find the existing record
            ],
            [
                'label' => 'Feature Preference',
                'link' => 'features',
                'params' => '{"permission":"Modules\\\\OpenAI\\\\Http\\\\Controllers\\\\Admin\\\\FeaturePreferenceController@manageFeature","route_name":["admin.features.feature_preference"]}',
                'is_default' => 1,
                'icon' => null,
                'parent' => 31,
                'sort' => 51,
                'class' => null,
                'menu' => 1,
                'depth' => 1,
                'is_custom_menu' => 0
            ]
        );
        

    }
}
