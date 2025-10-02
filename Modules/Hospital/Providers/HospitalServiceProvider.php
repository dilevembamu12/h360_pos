<?php

namespace Modules\Hospital\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Utils\ModuleUtil;
use App\Utils\Util;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Routing\Router;

class HospitalServiceProvider extends ServiceProvider
{
    /**
     * The filters base class name.
     *
     * @var array
     */
    protected $middleware = [
        'Hospital' => [
            'ContactSidebarMenu' => 'ContactSidebarMenu',
            'CheckContactLogin' => 'CheckContactLogin'
        ],
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerScheduleCommands();

        

        $this->registerMiddleware($this->app['router']);

        View::composer(
            ['hospital::layouts.nav'],
            function ($view) {
                $commonUtil = new Util();
                $is_admin = $commonUtil->is_admin(auth()->user(), auth()->user()->business_id);
                $view->with('__is_admin', $is_admin);
            }
        );
    }

    /**
     * Register the filters.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddleware(Router $router)
    {
        foreach ($this->middleware as $module => $middlewares) {
            foreach ($middlewares as $name => $middleware) {
                $class = "Modules\\{$module}\\Http\\Middleware\\{$middleware}";

                $router->aliasMiddleware($name, $class);
            }
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerCommands();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('hospital.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'hospital'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/hospital');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/hospital';
        }, config('view.paths')), [$sourcePath]), 'hospital');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/hospital');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'hospital');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'hospital');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            //\Modules\Hospital\Console\SendScheduleNotification::class,
            //\Modules\Hospital\Console\CreateRecursiveFollowup::class,
        ]);
    }

    public function registerScheduleCommands()
    {
        $env = config('app.env');
        $module_util = new ModuleUtil();
        $is_installed = $module_util->isModuleInstalled(config('hospital.name'));

        if ($env === 'live' && $is_installed) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command('pos:sendScheduleNotification')->everyMinute();
                $schedule->command('pos:createRecursiveFollowup')->daily();
            });
        }
    }
}




