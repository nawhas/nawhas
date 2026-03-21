<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('data')->create('stories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('hero_image_url')->nullable();
            $table->date('display_date')->nullable();
            $table->timestampTz('published_at')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::connection('data')->dropIfExists('stories');
    }
};
