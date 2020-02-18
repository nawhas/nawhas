<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Media;
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class MediaMapping extends EntityMapping
{
    public function mapFor()
    {
        return Media::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->string('type')->index();
        $map->string('uri');
        $map->timestamps();
    }
}
