<?php

declare(strict_types=1);

namespace Tests\Feature;

use Faker\Factory;
use Faker\Generator;
use Tests\Factories\InteractsWithFactories;
use Tests\TestCase;

abstract class FeatureTest extends TestCase
{
    use InteractsWithFactories;

    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    protected function setUpFaker(): void
    {
        $this->faker = Factory::create();
    }
}
