<?php

namespace App\Console\Commands\Migrate;

use Illuminate\Console\Command;

class MigrateEventsDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations for events DB.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->call('migrate', [
            '--database' => 'events',
            '--path' => 'database/migrations/events',
            '--force' => true,
        ]);
    }
}
