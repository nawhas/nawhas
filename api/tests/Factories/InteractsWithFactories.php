<?php

declare(strict_types=1);

namespace Tests\Factories;

trait InteractsWithFactories
{
    protected function getUserFactory(): UserFactory
    {
        return app(UserFactory::class);
    }

    protected function getReciterFactory(): ReciterFactory
    {
        return app(ReciterFactory::class);
    }
}
