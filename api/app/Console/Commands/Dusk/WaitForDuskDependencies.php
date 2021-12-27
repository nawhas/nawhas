<?php

declare(strict_types=1);

namespace App\Console\Commands\Dusk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;

class WaitForDuskDependencies extends Command
{
    private const NUXT_LOADING_STATE = '<title>Nuxt.js: Loading app...</title>';
    private const MAX_TRIES = 10;
    private const MAX_EXCEPTIONS = 4;

    protected $signature = 'dusk:wait';

    protected $description = 'Wait for web application to be ready for Dusk tests.';

    public function handle(Client $client): int
    {
        $tries = 0;
        $exceptions = 0;

        $this->info('Waiting for selenium to start...');
        $waitForIt = base_path('vendor/bin/wait-for-it.sh');
        passthru("$waitForIt selenium:4444");
        passthru("$waitForIt web:3000");
        passthru("$waitForIt nawhas.test:443");

        $this->info('Waiting for web application to start...');

        while ($tries < self::MAX_TRIES) {
            try {
                $response = $client->get($this->getBaseUrl(), [
                    'verify' => false, // Disable SSL checks
                ]);
                $body = (string)$response->getBody();

                if (!str_contains($body, self::NUXT_LOADING_STATE)) {
                    break;
                }

                $this->output->write('.');
            } catch (RequestException $e) {
                $exceptions++;
                $this->output->write('<error>E</error>');

                $response = optional($e->getResponse());

                logger()->debug('ResponseException caught.', [
                    'class' => get_class($e),
                    'message' => $e->getMessage(),
                    'status_code' => $response->getStatusCode(),
                    'body' => (string)$response->getBody(),
                ]);

                if ($exceptions > self::MAX_EXCEPTIONS) {
                    $this->output->newLine();
                    $this->error('Something\'s not right. Check the logs.');
                    return 1;
                }
            }

            $tries++;
            sleep(4);
        }

        $this->output->writeln('<info>R</info>');
        $this->output->success('Ready after ' . $tries . ' tries! Starting tests now.');

        $environment = app()->environment();
        $db = config('database.connections.' . config('database.default'));
        $this->output->info(
            <<<TEXT
            Dusk Configuration
            Environment: $environment
            DB Host: {$db['host']}
            DB Database: {$db['database']}
            TEXT
        );

        $health = json_decode(file_get_contents('http://nginx:80/alive'), true);
        $this->output->info(
            <<<TEXT
            API Configuration
            Environment: {$health['config']['environment']}
            DB Host: {$health['config']['database']['data']['host']}
            DB Database: {$health['config']['database']['data']['database']}
            TEXT
        );

        return 0;
    }

    private function getBaseUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }
}
