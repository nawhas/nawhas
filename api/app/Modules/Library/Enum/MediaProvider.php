<?php

declare(strict_types=1);

namespace App\Modules\Library\Enum;

enum MediaProvider: string
{
    case File = 'file';
    case Spotify = 'spotify';
    case YouTube = 'youtube';
}
