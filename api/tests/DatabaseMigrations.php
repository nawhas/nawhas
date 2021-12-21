<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseMigrations
{
    abstract public function artisan($command, $parameters = []);

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->artisan('migrate:all', ['--fresh' => true]);

        /**
         * @psalm-suppress NullArgument
         */
        $this->app[Kernel::class]->setArtisan(null);
    }
}
