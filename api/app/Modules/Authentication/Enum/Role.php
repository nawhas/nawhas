<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Enum;

enum Role: string
{
    case MODERATOR = 'moderator';
    case CONTRIBUTOR = 'contributor';
}
