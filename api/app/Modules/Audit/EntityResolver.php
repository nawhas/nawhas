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
    private const MAP = [
        'reciter' => Reciter::class,
        'album' => Album::class,
        'track' => Track::class,
        'lyrics' => Lyrics::class,
    ];

    public function toClassName(string $type): string
    {
        return self::MAP[$type];
    }

    public function toLabel(Entity $entity): string
    {
        $class = get_class($entity);
        $label = collect(self::MAP)->flip()->get($class);

        if ($label === null) {
            throw new InvalidArgumentException('No mapping found for ' . $class);
        }

        return $label;
    }
}
