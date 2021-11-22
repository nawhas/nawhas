<?php

declare(strict_types=1);

namespace App\Console\Commands\Migrate;

use Illuminate\Database\Console\Migrations\MigrateCommand;

class Migrate extends MigrateCommand
{
    use OperatesOnMultipleDatabases;

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        foreach ($this->getDatabases() as [$database => $path]) {
            $this->migrator->usingConnection($database, function () use ($path) {
                $this->prepareDatabase();

                $this->input->setOption('path', $path);

                // Next, we will check to see if a path option has been defined. If it has
                // we will use the path relative to the root of this installation folder
                // so that migrations may be run for any path within the applications.
                $this->migrator->setOutput($this->output)
                    ->run($this->getMigrationPaths(), [
                        'pretend' => $this->option('pretend'),
                        'step' => $this->option('step'),
                    ]);

                // Finally, if the "seed" option has been given, we will re-run the database
                // seed task to re-populate the database, which is convenient when adding
                // a migration and a seed at the same time, as it is only this command.
                if ($this->option('seed') && ! $this->option('pretend')) {
                    $this->call('db:seed', ['--force' => true]);
                }
            });
        }

        return 0;
    }
}
