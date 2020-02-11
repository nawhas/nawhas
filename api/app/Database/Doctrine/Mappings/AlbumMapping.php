<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Album;
use App\Entities\Reciter;
use LaravelDoctrine\Fluent\{EntityMapping,Fluent};

class AlbumMapping extends EntityMapping
{
    public function mapFor()
    {
        return Album::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->belongsTo(Reciter::class, 'reciter');
        $map->string('title');
        $map->unsignedSmallInteger('year');
        $map->string('artwork')->nullable();
        $map->timestamps();
    }
}
