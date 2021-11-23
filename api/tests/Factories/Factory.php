<?php

declare(strict_types=1);

namespace Tests\Factories;

use Faker\Generator;
use Faker\Factory as Faker;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class Factory
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    abstract protected function defaults(): array;

    protected function merge(array $attributes = []): ParameterBag
    {
        return new ParameterBag(
            collect($this->defaults())
                ->merge($attributes)
                ->mapWithKeys(fn ($value, $key) => [$key => value($value)])
                ->all()
        );
    }
}
