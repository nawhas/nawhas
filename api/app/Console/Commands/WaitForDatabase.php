<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Throwable;

class WaitForDatabase extends Command
{
    private const TIMEOUT = 20; // 20 seconds.
    protected $signature = 'wait:database';
    protected $description = 'Wait for the database.';
    /**
     * @var DatabaseManager
     */
    private DatabaseManager $manager;

    public function __construct(DatabaseManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function handle(): int
    {
        $this->comment('Looking for database connection...');

        $dataDb = $this->manager->connection('data');
        $eventsDb = $this->manager->connection('events');

        $tries = 0;
        while ($tries < self::TIMEOUT) {
            try {
                sleep(1);
                $tries++;
                $dataDb->unprepared('SELECT * FROM pg_catalog.pg_tables');
                $eventsDb->unprepared('SELECT * FROM pg_catalog.pg_tables');

                $this->info("Success! Found connection after $tries tries.");
                return 0;
            } catch (Throwable $e) {
                // Do nothing.
            }
        }

        $this->error('Timeout after 20 seconds. Connection not found.');
        return 1;
    }
}
