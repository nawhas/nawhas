<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Album;
use App\Entities\Reciter;
use App\Entities\Track;
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
        $map->string('year')->length(20);
        $map->string('artwork')->nullable();
        $map->hasMany(Track::class)->mappedBy('album')->cascadeAll();
        $map->timestamps();
    }
}
