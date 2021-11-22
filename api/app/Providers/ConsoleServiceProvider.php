<?php

namespace App\Providers;

use App\Console\Commands\Migrate\Migrate;
use App\Console\Commands\Migrate\MigrateFresh;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.migrate', function ($app) {
            return new Migrate($app['migrator']);
        });
        $this->app->singleton('command.migrate.fresh', function () {
            return new MigrateFresh();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
