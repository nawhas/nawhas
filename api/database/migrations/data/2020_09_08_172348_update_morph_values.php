<?php

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use Illuminate\Database\Migrations\Migration;

class UpdateMorphValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = DB::connection('data');

        $connection->table('visits')
            ->where('visitable_type', Reciter::class)
            ->update(['visitable_type' => EntityType::RECITER->value]);

        $connection->table('visits')
            ->where('visitable_type', Track::class)
            ->update(['visitable_type' => EntityType::TRACK->value]);

        $connection->table('visits')
            ->where('visitable_type', Album::class)
            ->update(['visitable_type' => EntityType::ALBUM->value]);

        $connection->table('saveables')
            ->where('saveable_type', Track::class)
            ->update(['saveable_type' => EntityType::TRACK->value]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
