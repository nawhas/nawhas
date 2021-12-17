<?php

declare(strict_types=1);

namespace App\Modules\Library\Enum;

enum MediaType: string
{
    case Audio = 'audio';
    case Video = 'video';
}
