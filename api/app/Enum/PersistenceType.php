<?php

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static self CREATE()
 * @method static self UPDATE()
 * @method static self DELETE()
 */
final class PersistenceType extends Enum
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
}
