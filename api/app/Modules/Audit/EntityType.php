<?php

declare(strict_types=1);

namespace App\Modules\Audit;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 *
 * @method static self RECITER()
 * @method static self ALBUM()
 * @method static self TRACK()
 * @method static self LYRICS()
 */
class EntityType extends Enum
{
    public const RECITER = 'reciter';
    public const ALBUM = 'album';
    public const TRACK = 'track';
    public const LYRICS = 'lyrics';
}
