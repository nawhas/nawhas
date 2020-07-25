<?php

namespace App\Console\Commands\Migrate;

use Illuminate\Console\Command;

class RollbackEventsMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollback:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback database migration for events DB.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->call('migrate:rollback', [
            '--database' => 'events',
            '--path' => 'database/migrations/events',
        ]);
    }
}
