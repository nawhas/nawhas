<?php declare(strict_types=1);

namespace App\Providers;

use App\Repositories\AlbumRepository;
use App\Repositories\Doctrine\DoctrineAlbumRepository;
use App\Repositories\Doctrine\DoctrineReciterRepository;
use App\Repositories\ReciterRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register()
    {
         $this->bindRepositories();
    }

    /**
     * @throws BindingResolutionException
     */
    private function bindRepositories(): void
    {
        $this->app->bind(ReciterRepository::class, DoctrineReciterRepository::class);
        $this->app->bind(AlbumRepository::class, DoctrineAlbumRepository::class);
    }
}
