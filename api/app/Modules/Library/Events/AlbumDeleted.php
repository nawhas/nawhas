<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\ChangeType;

class AlbumDeleted extends AlbumModified
{
    public function getChangeType(): ChangeType
    {
        return ChangeType::DELETED();
    }
}