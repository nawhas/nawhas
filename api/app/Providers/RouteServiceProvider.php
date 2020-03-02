<?php

namespace App\Providers;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Repositories\AlbumRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->app['request']->server->set('HTTPS','on');
        $this->bindEntities();
    }

    public function bindEntities(): void
    {
        Router::bind('reciter', function ($id, Route $route) {
            /** @var ReciterRepository $repo */
            $repo = $this->app->make(ReciterRepository::class);
            return $repo->get($id);
        });

        Router::bind('album', function ($id, Route $route) {
            /** @var AlbumRepository $repo */
            $repo = $this->app->make(AlbumRepository::class);

            $reciter = $route->parameter('reciter');
            if ($reciter instanceof Reciter) {
                return $repo->getByReciter($reciter, $id);
            }

            return $repo->get($id);
        });

        Router::bind('track', function ($id, Route $route) {
            /** @var TrackRepository $repo */
            $repo = $this->app->make(TrackRepository::class);

            $album = $route->parameter('album');
            if ($album instanceof Album) {
                return $repo->getFromAlbum($album, $id);
            }

            return $repo->get($id);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Router::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Router::middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
