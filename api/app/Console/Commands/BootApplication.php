<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;

class BootApplication extends Command
{
    protected $signature = 'boot';
    protected $description = 'Boot the application.';

    public function handle(Application $app): int
    {
        $this->comment('Booting application for ' . $app->environment());

        if ($app->isLocal()) {
            $this->info('Application booted.');
            return 0;
        }

        try {
            $this->all(
                'config:cache',
                'route:cache',
                'event-sourcing:cache-event-handlers',
                'wait:database',
                ['doctrine:migrations:migrate', [
                    '--force' => true, '--allow-no-migration' => true,
                ]],
                'doctrine:clear:metadata:cache',
                'doctrine:generate:proxies',
                'search:settings:push',
                'horizon:publish',
            );

            if ($app->environment('integration')) {
                $this->all('db:seed');
            }

            $this->all(
                'migrate:all'
            );

            if ($app->environment('integration')) {
                $this->call('event-sourcing:replay', ['--no-interaction' => true]);
            }
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
