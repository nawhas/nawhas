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
        $this->call('migrate:events');

        $this->warn('>> Running data migrations...');
        $this->call('migrate:data');

        $this->warn('>> Running other migrations...');
        $this->call('migrate', ['--force' => true]);
        
        return 0;
    }
}
