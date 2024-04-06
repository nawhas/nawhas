<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('data')->create('draft_lyrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('track_id')->unique();
            $table->json('document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('data')->dropIfExists('draft_lyrics');
    }
};
