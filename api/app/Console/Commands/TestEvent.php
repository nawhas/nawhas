<?php

namespace App\Console\Commands;

use App\Entities\Reciter;
use App\Entities\User;
use App\Enum\Role;
use App\Modules\Library\Events\ReciterCreated;
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
        event(new ReciterCreated(new Reciter('Asif Ali')));
        // event(new ReciterModified(new Reciter('Zain Mehdi')));
    }
}
