<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventVersionColumnToStoredEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('events')->table('stored_events', function (Blueprint $table) {
            $table->integer('event_version')->default(1);

            $table->unique(['aggregate_uuid', 'aggregate_version']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('events')->table('stored_events', function (Blueprint $table) {
            $table->dropColumn('event_version');
        });
    }
}
