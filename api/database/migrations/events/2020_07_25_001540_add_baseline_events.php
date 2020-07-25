<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\EventSourcing\Facades\Projectionist;
use Symfony\Component\Console\Output\ConsoleOutput;


class AddBaselineEvents extends Migration
{
    public function up(): void
    {
        $output = new ConsoleOutput();
        Artisan::call('events:initialize', [], $output);
    }

    public function down(): void
    {
        DB::connection('events')->table('stored_events')->truncate();
    }
}
