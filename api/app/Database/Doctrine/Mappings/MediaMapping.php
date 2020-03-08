<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\Media;
use App\Enum\MediaProvider;
use App\Enum\MediaType;
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
        $map->field(MediaType::class, 'type')->index();
        $map->field(MediaProvider::class, 'provider')->index();
        $map->string('path');
        $map->timestamps();
    }
}
