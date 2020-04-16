<?php

namespace App\Console\Commands;

use App\Database\Doctrine\EntityManager;
use App\Entities\Reciter;
use Illuminate\Console\Command;

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

    public function handle(EntityManager $em): int
    {
        // Check database connection.
        $start = microtime(true);
        $em->repository(Reciter::class)->count([]);
        $end = microtime(true);

        $this->info('Health check succeeded in ' . round($end - $start, 4) . 's');
        return 0;
    }
}
