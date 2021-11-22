<?php

declare(strict_types=1);

namespace App\Console\Commands\Migrate;

trait OperatesOnMultipleDatabases
{
    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     * @noinspection PhpMissingParamTypeInspection
     */
    abstract public function option($key = null);

    protected function getDatabases(): array
    {
        $database = $this->option('database');

        if ($database) {
            return [$database => $this->option('path')];
        }

        return [
            'events' => 'database/migrations/events',
            'data' => 'database/migrations/data',
        ];
    }
}
