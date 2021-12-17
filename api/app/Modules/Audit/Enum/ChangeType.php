<?php

declare(strict_types=1);

namespace App\Modules\Audit\Enum;

enum ChangeType: string
{
    case Created = 'created';
    case Modified = 'modified';
    case Deleted = 'deleted';
}
