<?php

namespace App\Console\Commands\Migrate;

use Illuminate\Console\Command;

class MigrateAllDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database migrations for all connections.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->warn('>> Running events migrations...');
        if ($this->option('fresh')) {
            $this->call('db:wipe', ['--database' => 'events']);
        }
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'database/migrations/events',
            '--database' => 'events',
        ]);

        $this->warn('>> Running data migrations...');
        if ($this->option('fresh')) {
            $this->call('db:wipe', ['--database' => 'data']);
        }
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'database/migrations/data'
        ]);

        $this->warn('>> Running package migrations...');
        $this->call('migrate', [
            '--force' => true,
        ]);

        return 0;
    }
}
