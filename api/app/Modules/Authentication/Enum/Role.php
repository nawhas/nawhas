<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Enum;

enum Role: string
{
    public const MODERATOR = 'moderator';
    public const CONTRIBUTOR = 'contributor';
}
