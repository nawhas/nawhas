<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static self MODERATOR()
 * @method static self CONTRIBUTOR()
 */
class Role extends Enum
{
    public const MODERATOR = 'moderator';
    public const CONTRIBUTOR = 'contributor';
}
