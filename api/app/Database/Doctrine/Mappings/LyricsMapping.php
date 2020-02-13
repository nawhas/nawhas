<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\{Lyrics, Track};
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class LyricsMapping extends EntityMapping
{
    public function mapFor()
    {
        return Lyrics::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->manyToOne(Track::class, 'track');
        $map->text('content');
        $map->timestamps();
    }
}
