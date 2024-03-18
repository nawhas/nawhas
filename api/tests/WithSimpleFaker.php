<?php

declare(strict_types=1);

namespace Tests;

use Faker\Factory;
use Faker\Generator;

trait WithSimpleFaker
{
    protected static ?Generator $faker = null;

    protected static function setUpFaker(): void
    {
        static::$faker = Factory::create();
    }

    protected static function faker(): Generator
    {
        if (static::$faker === null) {
            static::setUpFaker();
        }

        return static::$faker;
    }
}
