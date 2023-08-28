<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InspectEnvironment extends Command
{
    protected $signature = 'inspect:environment';
    protected $description = 'Get the current environment.';

    public function handle()
    {
        $this->line(env('APP_ENV', config('app.env')));
    }
}
