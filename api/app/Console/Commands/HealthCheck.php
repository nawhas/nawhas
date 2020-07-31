<?php

namespace App\Console\Commands;

use App\Modules\Authentication\Models\User;
use Illuminate\Console\Command;
use Laravel\Telescope\Telescope;

class HealthCheck extends Command
{
    /**
     * @var string
     */
    protected $signature = 'healthcheck';

    /**
     * @var string
     */
    protected $description = 'Run a health check.';

    public function handle(): int
    {
        // Check database connection.
        $start = microtime(true);
        Telescope::withoutRecording(fn () => User::count());
        $end = microtime(true);

        $this->info('Health check succeeded in ' . round($end - $start, 4) . 's');
        return 0;
    }
}
