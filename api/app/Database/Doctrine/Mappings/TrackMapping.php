<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Album;
use App\Entities\Lyrics;
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
        $map->belongsTo(Album::class, 'album')->inversedBy('tracks');
        $map->oneToOne(Lyrics::class, 'lyrics')->cascadePersist();
        $map->string('title');
        $map->string('slug')->length(191);
        $map->unique(['album_id', 'slug'])->name('unique_album_track_slug');
        $map->timestamps();
    }
}
