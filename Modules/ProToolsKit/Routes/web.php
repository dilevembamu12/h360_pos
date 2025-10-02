<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('protoolskit')->group(function() {
    Route::get('/', 'ProToolsKitController@index');
   
});


Route::middleware('web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu', 'superadmin')->prefix('protoolskit')->group(function () {
    Route::get('install', [\Modules\ProToolsKit\Http\Controllers\InstallController::class, 'index']);
    Route::post('install', [\Modules\ProToolsKit\Http\Controllers\InstallController::class, 'install']);
    Route::get('install/uninstall', [\Modules\ProToolsKit\Http\Controllers\InstallController::class, 'uninstall']);
    Route::get('install/update', [\Modules\ProToolsKit\Http\Controllers\InstallController::class, 'update']);

    //Route::resource('protoolskit-page', \Modules\ProToolsKit\Http\Controllers\CmsPageController::class)->except(['show']);
    //Route::resource('site-details', \Modules\ProToolsKit\Http\Controllers\SettingsController::class);
    
});

Route::middleware('web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu')->prefix('protoolskit')->group(function () {
    Route::get('/', 'SMSController@index');
    Route::get('/history', 'SMSController@history');
    Route::get('/buycredit', 'SMSController@buycredit');//paiement
    Route::get('/buycredit/{id}/pay', 'SMSController@paycredit');//processpaiement
    
    
         /******* custom_code 09062024  FLEXPAY_PAIMENT BUY CREDIT (SMS)*/
     //il doit etre avant les autres si je le met apres ca generer une erreur;
     Route::match(['get', 'post'],'/buycredit/flexpay-callback', [Modules\ProToolsKit\Http\Controllers\SMSController::class, 'confirm_flexpayBuycredit'])->name('confirm_flexpayBuycredit');
     Route::match(['get', 'post'],'/buycredit/{package_id}/flexpay-trigger', [Modules\ProToolsKit\Http\Controllers\SMSController::class, 'trigger_flexpay'])->name('triggerFlexpayBuycredit');
     /**************************************************** */

    



    Route::get('/showsendsms', 'SMSController@showSendSms');

    Route::post('/updatesmssenderid', 'SMSController@updateSmsSenderId');

    Route::post('/manualsendsms', 'SMSController@manualSendSms');
    
});