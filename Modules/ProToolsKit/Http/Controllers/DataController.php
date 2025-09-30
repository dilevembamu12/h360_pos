<?php

namespace Modules\ProToolsKit\Http\Controllers;

use Menu;
use Illuminate\Routing\Controller;

class DataController extends Controller
{
    public function superadmin_package()
    {
        return [
            [
                'name' => 'inventorymanagement_module',
                'label' => __('inventorymanagement::inventory.stock_inventory'),
                'default' => false
            ]
        ];
    }


    /**
     * Defines user permissions for the module.
     *
     * @return array
     */
    public function user_permissions()
    {
        $permissions = [
            [
                'value' => 'ptk.access_all_tools',
                'label' => __('ptk::lang.access_all_tools'),
                'default' => false,
                'is_radio' => true,
                'radio_input_name' => 'schedule_view',
            ],
        ];



        return $permissions;
    }


    /**
     * Adds hms menus
     *
     * @return null
     */
    public function modifyAdminMenu()
    {
        $background_color = '';
        if (config('app.env') == 'demo') {
            $background_color = '#C7E9C0  !important';
        }

        $menuparent = Menu::instance('admin-sidebar-menu');
        $menuparent->dropdown(
            __('protoolskit::ptk.ptk'),

            function ($sub) use ($background_color) {
                /*
                $sub->url(
                    action('\Modules\InventoryManagement\Http\Controllers\InventoryManagementController@index'),
                    __('protoolskit::ptk.show_ets_profil'),
                    ['active' => request()->segment(2) == 'createNewInventory']
                );
                */

                $sub->url(
                    action('\Modules\ProToolsKit\Http\Controllers\SMSController@index'),
                    __('protoolskit::ptk.show_sms'),
                    ['active' => request()->segment(2) == 'showSMS']
                );
            },

            ['icon' => 'fas fa fa-boxes', 'style' => "background-color:$background_color"]
        )->order(2);
     
    }
}
