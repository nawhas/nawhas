<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAliasesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('data')->create('reciter_aliases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reciter_id')->index();
            $table->foreign('reciter_id')->references('id')->on('reciters')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('alias')->unique();
            $table->timestamps();
        });
        Schema::connection('data')->create('album_aliases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reciter_id')->index();
            $table->foreign('reciter_id')->references('id')->on('reciters')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->uuid('album_id')->index();
            $table->foreign('album_id')->references('id')->on('reciters')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('alias');
            $table->timestamps();

            $table->unique(['reciter_id', 'alias']);
        });
        Schema::connection('data')->create('track_aliases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reciter_id')->index();
            $table->foreign('reciter_id')->references('id')->on('reciters')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->uuid('album_id')->index();
            $table->foreign('album_id')->references('id')->on('albums')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->uuid('track_id')->index();
            $table->foreign('track_id')->references('id')->on('tracks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('alias');
            $table->timestamps();

            $table->unique(['reciter_id', 'album_id', 'alias']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('data')->dropIfExists('reciter_aliases');
        Schema::connection('data')->dropIfExists('album_aliases');
        Schema::connection('data')->dropIfExists('track_aliases');
    }
}
