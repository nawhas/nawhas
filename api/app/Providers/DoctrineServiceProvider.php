<?php declare(strict_types=1);

namespace App\Providers;

use App\Database\Doctrine\Repositories as Doctrine;
use App\Entities\Reciter;
use App\Repositories\ReciterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindRepositories();
    }

    private function bindRepositories(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->app->make(EntityManagerInterface::class);

        $this->app->bind(
            ReciterRepository::class,
            fn() => new Doctrine\ReciterRepository($em->getRepository(Reciter::class))
        );
    }
}
