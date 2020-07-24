<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('data')->create('tracks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reciter_id');
            $table->uuid('album_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('audio')->nullable();
            $table->json('lyrics')->nullable();
            $table->timestamps();

            $table->unique(['album_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('data')->dropIfExists('tracks');
    }
}
