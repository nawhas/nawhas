<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\PersistenceType;

class AlbumCreated extends AlbumModified
{
    public function getPersistenceType(): PersistenceType
    {
        return PersistenceType::CREATE();
    }
}