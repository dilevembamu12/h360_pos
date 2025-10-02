<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Guard;
use App\Lib\AiProviderManager;
use App\Models\Model;
use Schema, View;
use App\Models\{
    Preference,
    Permission
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Check boot method is loaded or not.
     *
     * @var boolean
     */
    public $isBooted;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {
        Schema::defaultStringLength(191);

        // Will be used to prevent lazy loading (N+1 problem) in local environment
        // This will removed later
        Model::preventLazyLoading(! app()->isProduction());

        error_reporting(E_ALL);
        if (!$this->app->runningInConsole() && config('openAI.app_install') == true) {
            View::composer('*', function ($view) use ($auth) {
                if (!$this->isBooted) {
                    $json = \Cache::get('lanObject-' . config('app.locale'));
                    if (empty($json)) {
                        $json = file_get_contents(resource_path('lang/' . config('app.locale') . '.json'));
                        \Cache::put('lanObject-' . config('app.locale'), $json, 86400);
                    }
                    $data['json'] = $json;
                    $data['prms'] = Permission::getAuthUserPermission(optional($auth->user())->id);
                    $data['accessToken'] = !empty($auth->user()) && empty($auth->user()->token()) ? $auth->user()->createToken('accessToken')->accessToken : '';
                    $view->with($data);
                    $this->isBooted = true;
                }
            });
        }
        
        $this->app->bind(config('cache.prefix') . '.' . 'preferences', function () {
            return \Cache::rememberForever(config('cache.prefix') . '.' . 'preferences', function () {
                return Preference::pluck('value', 'field');
            });
        });
    }
    
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('all-image', function () {
            return objectStorage()->allFiles('public/uploads');
        });
        
        $this->app->singleton('image-directories', function () {
            return objectStorage()->allDirectories('public');
        });

        $this->app->singleton('aiprovidermanager', function ($app) {
            return new AiProviderManager;
        });
    }
}
