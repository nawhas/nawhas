<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateAllDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all';

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
        $this->warn('>> Running doctrine migrations...');
        $this->call('doctrine:migrations:migrate', [
            '--force' => true,
            '--allow-no-migration' => true,
        ]);

        $this->warn('>> Running events migrations...');
        $this->call('migrate', [
            '--database' => 'events',
            '--path' => 'database/migrations/events',
            '--force' => true,
        ]);

        $this->warn('>> Running data migrations...');
        $this->call('migrate', [
            '--database' => 'data',
            '--path' => 'database/migrations/data',
            '--force' => true,
        ]);
        return 0;
    }
}
