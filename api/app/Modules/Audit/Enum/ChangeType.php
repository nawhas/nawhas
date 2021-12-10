<?php

declare(strict_types=1);

namespace App\Modules\Audit\Enum;

enum ChangeType: string
{
    case CREATED = 'created';
    case MODIFIED = 'modified';
    case DELETED = 'deleted';
}
