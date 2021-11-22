<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class ResetCachedConfigs extends Command
{
    protected $signature = 'app:reset';
    protected $description = 'Reset production config caches.';

    public function handle(): int
    {
        $commands = [
            'config:clear',
            'route:clear',
            'event-sourcing:clear-event-handlers',
            'clear-compiled'
        ];

        try {
            $this->all(...$commands);
        } catch (Exception $e) {
            $this->error("Failed to clear application configs.\n{$e->getMessage()}");
            return 1;
        }

        $this->info('Application configs cleared.');
        return 0;
    }

    private function all(...$commands): void
    {
        foreach ($commands as $command) {
            if (is_array($command)) {
                $code = $this->call(...$command);
            } else {
                $code = $this->call($command);
            }

            if ($code !== 0) {
                throw new Exception("Command '$command' failed with exit code $code");
            }
        }
    }
}
