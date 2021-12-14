<?php

declare(strict_types=1);

namespace Tests;

use Faker\Factory;
use Faker\Generator;

trait WithSimpleFaker
{
    protected Generator $faker;

    protected function setUpFaker(): void
    {
        $this->faker = Factory::create();
    }
}
