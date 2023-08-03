<?php

namespace App\Providers;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use Illuminate\Database\Eloquent\Relations\Relation;
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

        if ($this->app->environment('local', 'staging')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(Fractal::class, function (): Fractal {
            $fractal = new Fractal();

            $include = request('include');
            if ($include) {
                $fractal->parseIncludes($include);
            }

            $fractal->setSerializer(new ArraySerializer());

            return $fractal;
        });

        Relation::morphMap([
            EntityType::Reciter->value => Reciter::class,
            EntityType::Album->value => Album::class,
            EntityType::Track->value => Track::class,
        ]);
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
