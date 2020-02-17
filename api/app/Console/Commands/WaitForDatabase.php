<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Throwable;

class WaitForDatabase extends Command
{
    private const TIMEOUT = 20; // 20 seconds.
    protected $signature = 'wait:database';
    protected $description = 'Wait for the database.';
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    public function handle()
    {
        $this->comment('Looking for database connection...');

        $tries = 0;
        while ($tries < self::TIMEOUT) {
            try {
                sleep(1);
                $tries++;
                $this->connection->unprepared('SHOW TABLES;');

                $this->info("Success! Found connection after $tries tries.");
                return;
            } catch (Throwable $e) {
                // Do nothing.
            }
        }

        $this->warn('Timeout after 20 seconds. Connection not found.');
        return;
    }
}
