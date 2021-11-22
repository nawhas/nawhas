<?php

declare(strict_types=1);

namespace App\Console\Commands\Migrate;

use Illuminate\Database\Console\Migrations\FreshCommand;

class MigrateFresh extends FreshCommand
{
    use OperatesOnMultipleDatabases;

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        foreach ($this->getDatabases() as $database => $path) {
            $this->call('db:wipe', array_filter([
                '--database' => $database,
                '--drop-views' => $this->option('drop-views'),
                '--drop-types' => $this->option('drop-types'),
                '--force' => true,
            ]));

            $this->call('migrate', array_filter([
                '--database' => $database,
                '--path' => $path,
                '--force' => true,
                '--step' => $this->option('step'),
            ]));


            if ($this->needsSeeding()) {
                $this->runSeeder($database);
            }
        }

        return 0;
    }
}
