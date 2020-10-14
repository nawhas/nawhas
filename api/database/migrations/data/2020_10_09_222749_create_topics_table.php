<?php

use App\Modules\Library\Models\Topic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('data')->create('topics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $this->seedInitialTopics();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('data')->dropIfExists('topics');
    }

    private function seedInitialTopics()
    {
        Topic::create('Imam Ali');
        Topic::create('Imam Hassan');
        Topic::create('Imam Hussain');
        Topic::create('Imam Zain al-Abideen');
        Topic::create('Hazrat Abbas');
        Topic::create('Hazrat Ali Akbar');
        Topic::create('Hazrat Ali Asghar');
        Topic::create('Hazrat Qasim');
        Topic::create('Hazrat Aun-o-Muhammad');
        Topic::create('Hazrat Hur');
        Topic::create('Hazrat Muslim ibn Aqeel');
        Topic::create('Bibi Zainab');
        Topic::create('Bibi Sakina');
        Topic::create('Bibi Fatima Zehra');
        Topic::create('Bibi Fatima Sughra');
        Topic::create('Bibi Umm Kulthum');
    }
}
