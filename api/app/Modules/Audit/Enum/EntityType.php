<?php

declare(strict_types=1);

namespace App\Modules\Audit\Enum;

enum EntityType: string
{
    case Reciter = 'reciter';
    case Album = 'album';
    case Track = 'track';
    case Topic = 'topic';
}
