<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\PersistenceType;

class TrackDeleted extends TrackModified
{
    public function getPersistenceType(): PersistenceType
    {
        return PersistenceType::DELETE();
    }
}