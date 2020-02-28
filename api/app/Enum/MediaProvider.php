<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static self FILE()
 * @method static self SPOTIFY()
 * @method static self YOUTUBE()
 */
class MediaProvider extends Enum
{
    public const FILE = 'file';
    public const SPOTIFY = 'spotify';
    public const YOUTUBE = 'youtube';
}
