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
            ->update(['visitable_type' => EntityType::RECITER]);

        $connection->table('visits')
            ->where('visitable_type', Track::class)
            ->update(['visitable_type' => EntityType::TRACK]);

        $connection->table('visits')
            ->where('visitable_type', Album::class)
            ->update(['visitable_type' => EntityType::ALBUM]);

        $connection->table('saveables')
            ->where('saveable_type', Track::class)
            ->update(['saveable_type' => EntityType::TRACK]);
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
