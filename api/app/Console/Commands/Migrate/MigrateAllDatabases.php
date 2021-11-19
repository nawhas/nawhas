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
        $args = $this->option('fresh') ? ['--fresh' => true] : [];

        $this->warn('>> Running events migrations...');
        $this->call('migrate:events', $args);

        $this->warn('>> Running data migrations...');
        $this->call('migrate:data', $args);

        $this->warn('>> Running other migrations...');
        if ($this->option('fresh')) {
            $this->call('migrate:reset');
        }
        $this->call('migrate', ['--force' => true]);

        return 0;
    }
}
