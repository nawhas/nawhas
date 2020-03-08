<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings\Visits;

use App\Entities\Track;
use App\Visits\Entities\TrackVisit as Visit;
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class TrackVisitMapping extends EntityMapping
{
    public function mapFor()
    {
        return Visit::class;
    }

    public function map(Fluent $map)
    {
        $map->belongsTo(Track::class)->inversedBy('visits');
    }
}
