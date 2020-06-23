<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\PersistenceType;

class AlbumDeleted extends AlbumModified
{
    public function getPersistenceType(): PersistenceType
    {
        return PersistenceType::DELETE();
    }
}