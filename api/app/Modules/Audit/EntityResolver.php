<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use App\Entities\Album;
use App\Entities\Contracts\Entity;
use App\Entities\Lyrics;
use App\Entities\Reciter;
use App\Entities\Track;
use InvalidArgumentException;

class EntityResolver
{
    public function toClassName(string $type): string
    {
        return [
            EntityType::RECITER => Reciter::class,
            EntityType::ALBUM => Album::class,
            EntityType::TRACK => Track::class,
            EntityType::LYRICS => Lyrics::class,
        ][$type];
    }

    public function toLabel(Entity $entity): string
    {
        switch (true) {
            case $entity instanceof Reciter:
                return EntityType::RECITER;
            case $entity instanceof Album:
                return EntityType::ALBUM;
            case $entity instanceof Track:
                return EntityType::TRACK;
            case $entity instanceof Lyrics:
                return EntityType::LYRICS;
            default:
                throw new InvalidArgumentException("Unknown entity type " . get_class($entity));
        }
    }
}
