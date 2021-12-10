<?php

declare(strict_types=1);

namespace App\Modules\Audit\Enum;

enum EntityType: string
{
    case RECITER = 'reciter';
    case ALBUM = 'album';
    case TRACK = 'track';
}
