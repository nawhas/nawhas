<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Track;
use App\Enum\PersistenceType;
use App\Modules\Audit\Events\AuditableEvent;

class TrackModified implements EntityPersisted, AuditableEvent
{
    public Track $track;

    public function __construct(Track $track)
    {
        $this->track = $track;
    }

    public function getEntity(): Track
    {
        return $this->track;
    }

    public function getEntityType(): string
    {
        return Track::class;
    }

    public function getPersistenceType(): PersistenceType
    {
        return PersistenceType::UPDATE();
    }
}