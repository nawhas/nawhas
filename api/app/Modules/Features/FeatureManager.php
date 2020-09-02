<?php

declare(strict_types=1);

namespace App\Modules\Features;

use App\Modules\Authentication\Guard;
use App\Modules\Features\Definitions\Feature;
use App\Modules\Features\Exceptions\FeatureNotRegisteredException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

class FeatureManager
{
    private Guard $guard;
    private Container $container;

    /** @var array<string,Feature>|Collection|Feature[] */
    private Collection $features;

    public function __construct(Guard $guard, Container $container)
    {
        $this->guard = $guard;
        $this->container = $container;
        $this->features = new Collection();
    }

    public function enabled(string $name): bool
    {
        $instance = $this->resolve($name);

        return $instance->enabled($this->guard->user());
    }

    private function resolve(string $name): Feature
    {
        $feature = $this->features->get($name);

        if (!$feature) {
            throw new FeatureNotRegisteredException($name);
        }

        return $feature;
    }

    public function register(Feature $feature): void
    {
        $this->features->put($feature->name(), $feature);
    }

    public function export(): array
    {
        $export = [];

        foreach ($this->features as $feature) {
            $export[$feature->name()] = $this->enabled($feature->name());
        }

        return $export;
    }
}
