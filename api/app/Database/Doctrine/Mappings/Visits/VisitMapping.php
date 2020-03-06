<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings\Visits;

use App\Visits\Entities\Visit;
use LaravelDoctrine\Fluent\{Fluent, MappedSuperClassMapping};

class VisitMapping extends MappedSuperClassMapping
{
    public function mapFor()
    {
        return Visit::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->date('date');
    }
}
