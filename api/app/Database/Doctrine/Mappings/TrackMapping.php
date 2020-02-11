<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Entities\Track;
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class TrackMapping extends EntityMapping
{
    public function mapFor()
    {
        return Track::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->belongsTo(Album::class, 'album');
        $map->belongsTo(Reciter::class, 'reciter');
        $map->string('title');
        $map->timestamps();
    }
}
