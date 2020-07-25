<?php

namespace App\Console\Commands\Migrate;

use Illuminate\Console\Command;

class RollbackDataMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollback:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback database migration for data DB.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return $this->call('migrate:rollback', [
            '--database' => 'data',
            '--path' => 'database/migrations/data',
        ]);
    }
}
