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
    private const MAX_EXCEPTIONS = 2;

    protected $signature = 'dusk:wait';

    protected $description = 'Wait for web application to be ready for Dusk tests.';

    public function handle(Client $client): int
    {
        $tries = 0;
        $exceptions = 0;

        $this->info('Waiting for selenium to start...');
        $waitForIt = base_path('vendor/bin/wait-for-it.sh');
        passthru("$waitForIt selenium:4444");
        passthru("$waitForIt nawhas.test:3000");

        $this->info('Waiting for web application to start...');

        while ($tries < self::MAX_TRIES) {
            try {
                $response = $client->get($this->getBaseUrl());
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

        return $this->call('dusk');
    }

    private function getBaseUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }
}
