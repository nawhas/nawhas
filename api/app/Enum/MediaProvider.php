<?php

declare(strict_types=1);

namespace App\Enum;

enum MediaProvider: string
{
    case FILE = 'file';
    case SPOTIFY = 'spotify';
    case YOUTUBE = 'youtube';
}
