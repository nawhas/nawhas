<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings\Visits;

use App\Entities\Reciter;
use App\Visits\Entities\ReciterVisit as Visit;
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class ReciterVisitMapping extends EntityMapping
{
    public function mapFor()
    {
        return Visit::class;
    }

    public function map(Fluent $map)
    {
        $map->belongsTo(Reciter::class)->inversedBy('visits');
    }
}
