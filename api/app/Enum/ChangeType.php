<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static self CREATED()
 * @method static self UPDATED()
 * @method static self DELETED()
 */
final class ChangeType extends Enum
{
    public const CREATED = 'created';
    public const UPDATED = 'updated';
    public const DELETED = 'deleted';
}
