<?php

namespace EomPlus\Version;

use EomPlus\Version\Console\Commands;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->publishes([
            __DIR__.'/../config/version.php' => config_path('version.php'),
        ], 'version');

        $this->app->singleton('version', function ($app) {
            return new Version($app);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        $this->mergeConfigFrom(__DIR__.'/../config/version.php', 'version');
    }

    protected function registerCommands()
    {
        $this->commands([
            Commands\Version::class,
            Commands\VersionInit::class,
        ]);

    }
}
