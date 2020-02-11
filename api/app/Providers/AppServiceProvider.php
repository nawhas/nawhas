<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
