<?php

namespace App\Console\Commands;

use App\Entities\Reciter;
use App\Modules\Library\Events\ReciterCreated;
use App\Modules\Library\Events\ReciterModified;
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
     * @return mixed
     */
    public function handle()
    {
        event(new ReciterCreated(new Reciter('Zain Mehdi')));
        event(new ReciterModified(new Reciter('Zain Mehdi')));
    }
}
