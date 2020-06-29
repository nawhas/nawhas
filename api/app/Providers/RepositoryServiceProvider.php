<?php

namespace App\Providers;

use App\Repositories\AlbumRepository;
use App\Repositories\Cache\CachedPopularEntitiesRepository;
use App\Repositories\Doctrine\DoctrineAlbumRepository;
use App\Repositories\Doctrine\DoctrinePopularEntitiesRepository;
use App\Repositories\Doctrine\DoctrineReciterRepository;
use App\Repositories\Doctrine\DoctrineSocialAccountRepository;
use App\Repositories\Doctrine\DoctrineTrackRepository;
use App\Repositories\Doctrine\DoctrineUserRepository;
use App\Repositories\PopularEntitiesRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\SocialAccountRepository;
use App\Repositories\TrackRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ReciterRepository::class, DoctrineReciterRepository::class);
        $this->app->bind(AlbumRepository::class, DoctrineAlbumRepository::class);
        $this->app->bind(TrackRepository::class, DoctrineTrackRepository::class);
        $this->app->bind(PopularEntitiesRepository::class, DoctrinePopularEntitiesRepository::class);
        $this->app->bind(SocialAccountRepository::class, DoctrineSocialAccountRepository::class);
        $this->app->bind(UserRepository::class, DoctrineUserRepository::class);

        // Cached Repositories
        /*
        $this->app->bind(PopularEntitiesRepository::class, CachedPopularEntitiesRepository::class);
        $this->app->when(CachedPopularEntitiesRepository::class)
            ->needs(PopularEntitiesRepository::class)
            ->give(DoctrinePopularEntitiesRepository::class);
        */
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
