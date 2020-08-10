<?php

namespace App\Console\Commands\Meilisearch;

use Illuminate\Console\Command;

class SetupScout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:setup {--reset}';

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
    public function handle()
    {
        $indices = config('meilisearch.indices');

        foreach ($indices as $index => $config) {
            if ($this->option('reset')) {
                $this->call('scout:index', [
                    'name' => $index,
                    '--delete' => true,
                ]);
            }

            $this->call('scout:index', [
               'name' => $index
            ]);
            $this->call('scout:import', [
                'model' => $config['model'],
            ]);
        }

        return 0;
    }
}
