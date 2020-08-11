<?php

namespace App\Console\Commands\Meilisearch;

use Illuminate\Console\Command;
use MeiliSearch\Client as Meilisearch;

class SetupMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:setup {--reset} {--import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Meilisearch indices';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Meilisearch $client)
    {
        $indices = config('meilisearch.indices');

        foreach ($indices as $index => $config) {
            if ($this->option('reset')) {
                $this->call('scout:index', [
                    'name' => $index,
                    '--delete' => true,
                ]);
            }

            $this->comment("Creating index $index");
            $this->call('scout:index', [
               'name' => $index
            ]);

            $client->getIndex($index)->updateSettings($config['settings']);
            $this->comment("Pushed settings for index $index");

            if ($this->option('import')) {
                $this->call('scout:import', [
                    'model' => $config['model'],
                ]);
            }
        }

        return 0;
    }
}
