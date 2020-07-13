<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use MyCLabs\Enum\Enum;

class EntityType extends Enum
{
    public const RECITER = 'reciter';
    public const ALBUM = 'album';
    public const TRACK = 'track';
    public const LYRICS = 'lyrics';
}
