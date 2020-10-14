<?php

declare(strict_types=1);

namespace App\Modules\Audit\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static EntityType RECITER()
 * @method static EntityType ALBUM()
 * @method static EntityType TRACK()
 * @method static EntityType TOPIC()
 */
final class EntityType extends Enum
{
    public const RECITER = 'reciter';
    public const ALBUM = 'album';
    public const TRACK = 'track';
    public const TOPIC = 'topic';
}
