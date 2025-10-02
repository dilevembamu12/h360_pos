<?php

// Fichier : Modules/Help/Routes/web.php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE PUBLIQUE (pour le popup vidéo) ---
// Pas besoin de groupe ici, le RouteServiceProvider s'en charge.
Route::get('/help/get-videos-for-url', 'VideoTutorialController@fetchVideosForUrl')->name('help.fetchVideos');


// --- ROUTES D'ADMINISTRATION (protégées et avec le menu latéral) ---
Route::group(['middleware' => ['web', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'help', 'as' => 'help.'], function() {
    
    // Page principale de l'Académie
    Route::get('academy', 'AcademyController@index')->name('academy.index');

    // Routes de gestion (protégées par le middleware admin)
    Route::group(['middleware' => ['is_admin_user']], function() {
        Route::resource('video-tutorials', 'VideoTutorialController');
        Route::resource('onboarding', 'OnboardingController');
    });

});

// --- ROUTES D'INSTALLATION (protégées pour le superadmin) ---
Route::group(['middleware' => ['web', 'auth', 'SetSessionData', 'AdminSidebarMenu', 'superadmin']], function () {
    Route::get('install/help-module', [\Modules\Help\Http\Controllers\InstallController::class, 'index']);
    Route::post('install/help-module', [\Modules\Help\Http\Controllers\InstallController::class, 'install']);
    Route::get('install/uninstall-help-module', [\Modules\Help\Http\Controllers\InstallController::class, 'uninstall']);
});