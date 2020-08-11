<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use League\Fractal\Manager as Fractal;
use League\Fractal\Serializer\ArraySerializer;
use MeiliSearch\Client as Search;

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

        $this->app->bind(Search::class, fn () => new Search(config('meilisearch.host'), config('meilisearch.key')));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
