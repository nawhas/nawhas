<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test event firing and listening';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // event(new ReciterCreated(new Reciter('Zain Mehdi')));
        // event(new ReciterModified(new Reciter('Zain Mehdi')));
    }
}
