<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static self AUDIO()
 * @method static self VIDEO()
 */
class MediaType extends Enum
{
    public const AUDIO = 'audio';
    public const VIDEO = 'video';
}
