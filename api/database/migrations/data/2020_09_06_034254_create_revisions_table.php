<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('data')->create('revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('entity');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('change_type');
            $table->uuid('user_id')->nullable()->index();
            $table->bigInteger('version');
            $table->bigInteger('event_id')->index();
            $table->dateTime('created_at');

            $table->unique(['entity_type', 'entity_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('data')->dropIfExists('revisions');
    }
}
