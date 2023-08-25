<?php

declare(strict_types=1);

namespace App\Console\Commands\Search;

use Illuminate\Console\Command;
use Meilisearch\Client as Meilisearch;

class SetUpSearch extends Command
{
    protected $signature = 'search:setup {--reset} {--import}';

    protected $description = 'Setup search indices, settings, etc.';

    private Meilisearch $client;

    public function handle(): int
    {
        $this->client = new Meilisearch('http://search:7700', 'secret');
        $prefix = config('scout.prefix');
        $indices = collect(config('scout.indices'));


        if ($this->option('reset')) {
            $tasks = $indices->keys()->map(fn ($index) => $this->client->deleteIndex($prefix . $index)['taskUid'])->all();
            $this->client->waitForTasks($tasks);
            $this->comment(">> 🗑  Indexes deleted.");
        }

        $tasks = $indices->keys()->map(fn ($index) => $this->client->createIndex($prefix . $index, ['primaryKey' => 'id'])['taskUid'])->all();
        $this->client->waitForTasks($tasks);
        $this->comment(">> ✅ Indexes created");


        if ($this->option('import')) {
            $indices->map(fn ($config) => $this->call('scout:import', [
                'model' => $config['model'],
            ]));
        }

        $this->info('✅✅ Done!');

        return 0;
    }
}
