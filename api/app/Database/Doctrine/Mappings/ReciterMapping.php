<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Reciter;
use App\Visits\Entities\ReciterVisit;
use LaravelDoctrine\Fluent\{EntityMapping,Fluent};

class ReciterMapping extends EntityMapping
{
    public function mapFor()
    {
        return Reciter::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->string('name');
        $map->string('slug')->length(191)->unique();
        $map->text('description')->nullable();
        $map->string('avatar')->nullable();
        $map->hasMany(ReciterVisit::class, 'visits')->mappedBy('reciter');
        $map->timestamps();
    }
}
