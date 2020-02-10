<?php declare(strict_types=1);

namespace App\Providers;

use App\Database\Doctrine\Repositories as Doctrine;
use App\Entities\Album;
use App\Entities\Reciter;
use App\Repositories\AlbumRepository;
use App\Repositories\ReciterRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        /** @var EntityManagerInterface $em */
        $em = $this->app->make(EntityManagerInterface::class);

        $this->app->bind(
            ReciterRepository::class,
            fn() => new Doctrine\ReciterRepository($em->getRepository(Reciter::class))
        );
        $this->app->bind(
            AlbumRepository::class,
            fn() => new Doctrine\AlbumRepository($em->getRepository(Album::class))
        );
    }
}
