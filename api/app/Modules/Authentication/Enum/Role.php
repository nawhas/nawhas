<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Enum;

enum Role: string
{
    case Moderator = 'moderator';
    case Contributor = 'contributor';
}
