<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    public function up()
    {
        Schema::connection('data')->create('library_saves', function (Blueprint $table) {
            $table->uuid('user_id')->index();
            $table->string('saveable_type')->index();
            $table->uuid('saveable_id')->index();
            $table->timestamp();
        });
    }

    public function down()
    {
        Schema::connection('data')->dropIfExists('library_saves');
    }
}
