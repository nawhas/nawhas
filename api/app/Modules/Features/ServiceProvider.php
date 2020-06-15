<?php

declare(strict_types=1);

namespace App\Modules\Features;

use App\Modules\Authentication\Guard;
use App\Modules\Features\Definitions\Feature;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class ServiceProvider extends IlluminateServiceProvider
{
    private const DEFINITIONS_PATH = __DIR__ . '/Definitions';

    public function register()
    {
        $this->app->bind(FeatureManager::class, function (): FeatureManager {
            $manager = new FeatureManager(
                $this->app->make(Guard::class),
                $this->app->make(Container::class)
            );

            $this->registerFeatures($manager);

            return $manager;
        });
    }

    public function registerFeatures(FeatureManager $manager): void
    {

        $namespace = $this->app->getNamespace();

        foreach ((new Finder())->in(self::DEFINITIONS_PATH)->files() as $file) {
            $feature = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getPathname(), realpath(app_path()) . DIRECTORY_SEPARATOR)
            );

            if (is_subclass_of($feature, Feature::class) && !(new ReflectionClass($feature))->isAbstract()) {
                $manager->register($this->app->make($feature));
            }
        }
    }
}
