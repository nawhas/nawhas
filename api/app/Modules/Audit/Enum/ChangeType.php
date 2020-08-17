<?php

declare(strict_types=1);

namespace App\Modules\Audit\Enum;

use MyCLabs\Enum\Enum;

/**
 * @psalm-immutable
 * @method static ChangeType CREATED()
 * @method static ChangeType MODIFIED()
 * @method static ChangeType DELETED()
 */
final class ChangeType extends Enum
{
    public const CREATED = 'created';
    public const MODIFIED = 'modified';
    public const DELETED = 'deleted';
}
