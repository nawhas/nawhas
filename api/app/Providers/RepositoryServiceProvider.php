<?php

namespace App\Providers;

use App\Modules\Audit\Repositories\{AuditRepository, DoctrineAuditRepository};
use App\Repositories\Doctrine\{
    DoctrineAlbumRepository,
    DoctrinePopularEntitiesRepository,
    DoctrineReciterRepository,
    DoctrineSocialAccountRepository,
    DoctrineTrackRepository,
    DoctrineUserRepository,
};
use App\Repositories\{
    AlbumRepository,
    PopularEntitiesRepository,
    ReciterRepository,
    SocialAccountRepository,
    TrackRepository,
    UserRepository,
};
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
        $this->app->bind(AuditRepository::class, DoctrineAuditRepository::class);
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
