<?php

declare(strict_types=1);

namespace App\Console\Commands\Search;

use Illuminate\Console\Command;
use MeiliSearch\Client as Meilisearch;

class SetUpSearch extends Command
{
    protected $signature = 'search:setup {--reset} {--import}';

    protected $description = 'Setup search indices, settings, etc.';

    public function handle(Meilisearch $client): int
    {
        $prefix = config('scout.prefix');
        $indices = collect(config('scout.indices'));


        if ($this->option('reset')) {
            $tasks = $indices->keys()->map(fn ($index) => $client->deleteIndex($prefix . $index)['taskUid'])->all();
            $client->waitForTasks($tasks);
            $this->comment(">> 🗑  Indexes deleted.");
        }

        $tasks = $indices->keys()->map(fn ($index) => $client->createIndex($prefix . $index, ['primaryKey' => 'id'])['taskUid'])->all();
        $client->waitForTasks($tasks);
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
