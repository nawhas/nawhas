<?php

namespace App\Providers;

use App\Console\Commands\ImportDataCommand;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use League\Fractal\Manager as Fractal;
use League\Fractal\Serializer\ArraySerializer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();

        $this->app->singleton(Fractal::class, function (): Fractal {
            $fractal = new Fractal();

            $include = request('include');
            if ($include) {
                $fractal->parseIncludes($include);
            }

            $fractal->setSerializer(new ArraySerializer());

            return $fractal;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
