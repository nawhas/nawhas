<?php

declare(strict_types=1);

namespace App\Console\Commands\Migrate;

use Illuminate\Database\Console\Migrations\RollbackCommand;

class MigrateRollback/* extends RollbackCommand*/
{
//    use OperatesOnMultipleDatabases;

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        foreach ($this->getDatabases() as [$database => $path]) {
            $this->migrator->usingConnection($database, function () use ($path) {
                $this->input->setOption('path', $path);

                $this->migrator->setOutput($this->output)->rollback(
                    $this->getMigrationPaths(), [
                        'pretend' => $this->option('pretend'),
                        'step' => (int) $this->option('step'),
                    ]
                );
            });
        }

        return 0;
    }
}
