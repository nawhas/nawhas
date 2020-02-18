<?php

declare(strict_types=1);

namespace App\Providers;

use Acelaya\Doctrine\Type\PhpEnumType;
use App\Repositories\AlbumRepository;
use App\Repositories\Doctrine\DoctrineAlbumRepository;
use App\Repositories\Doctrine\DoctrineReciterRepository;
use App\Repositories\Doctrine\DoctrineTrackRepository;
use App\Repositories\ReciterRepository;
use App\Repositories\TrackRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use MyCLabs\Enum\Enum;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class DoctrineServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register()
    {
         $this->bindRepositories();
         $this->registerEnumTypes();
    }

    /**
     * @throws BindingResolutionException
     */
    private function bindRepositories(): void
    {
        $this->app->bind(ReciterRepository::class, DoctrineReciterRepository::class);
        $this->app->bind(AlbumRepository::class, DoctrineAlbumRepository::class);
        $this->app->bind(TrackRepository::class, DoctrineTrackRepository::class);
    }

    private function registerEnumTypes(): void
    {
        $finder = new Finder();

        $finder->files()->name('*.php')->in(__DIR__ . '/../Enum/');

        foreach ($finder as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file **/
            $ns = 'App\Enum';

            if ($relativePath = $file->getRelativePath()) {
                $ns .= '\\' . strtr($relativePath, '/', '\\');
            }

            $className = $ns . '\\' . $file->getBasename('.php');

            $class = new ReflectionClass($className);

            if ($class->isSubclassOf(Enum::class) && !$class->isAbstract() && !PhpEnumType::hasType($className)) {
                PhpEnumType::registerEnumType($className);
            }
        }
    }
}
