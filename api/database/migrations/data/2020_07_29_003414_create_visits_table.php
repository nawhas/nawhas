<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    public function up()
    {
        Schema::connection('data')->create('visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('visitable_id');
            $table->string('visitable_type');
            $table->date('date');
        });
    }

    public function down()
    {
        Schema::connection('data')->dropIfExists('visits');
    }
}
