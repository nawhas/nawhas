<?php

namespace App\Console\Commands\Revisions;

use App\Modules\Audit\Support\DiscoverRevisionableEvents;
use Illuminate\Console\Command;

class CacheEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:cache-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache revisionable events';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $events = (new DiscoverRevisionableEvents())->forget()->discover();
        $this->info(count($events) . ' revisionable events cached.');
        return 0;
    }
}
