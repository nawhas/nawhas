<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class BootApplication extends Command
{
    use ConditionallyLoadsAttributes;

    protected $signature = 'boot';
    protected $description = 'Boot the application.';

    public function handle(Application $app): int
    {
        $this->comment('Booting application for ' . $app->environment());

        $commands = [
            $this->when($app->environment('integration', 'staging', 'production'), [
                'package:discover',
                'config:cache',
                'route:cache',
                'event-sourcing:cache-event-handlers',
                'search:settings:push',
                'horizon:publish',
                'telescope:publish',
            ]),
            'wait:database',
            'migrate:all',
            $this->when($app->environment('integration'), [
                'db:seed',
            ]),
        ];

        try {
            $this->all(...$this->filter($commands));
        } catch (Exception $e) {
            $this->error("Failed to boot application.\n{$e->getMessage()}");
            return 1;
        }

        $this->info('Application booted.');
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
