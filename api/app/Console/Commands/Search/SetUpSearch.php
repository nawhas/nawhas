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
        $indices = config('scout.indices');

        foreach ($indices as $index => $config) {
            $index = $prefix . $index;

            if ($this->option('reset')) {
                $client->deleteIndexIfExists($index);
                $this->comment(">> 🗑  Index \"$index\" deleted.");
            }

            $client->getOrCreateIndex($index)->updateSettings($config['settings']);
            $this->comment(">> ✅ Index \"$index\" created");

            if ($this->option('import')) {
                $this->call('scout:import', [
                    'model' => $config['model'],
                ]);
            }
        }

        $this->info('Done!');

        return 0;
    }
}
