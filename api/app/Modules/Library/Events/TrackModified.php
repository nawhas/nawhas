<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Track;
use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Events\AuditableEvent;

class TrackModified implements EntityPersisted, AuditableEvent
{
    public Track $track;
    public User $user;

    public function __construct(Track $track, User $user)
    {
        $this->track = $track;
        $this->user = $user;
    }

    public function getEntity(): Track
    {
        return $this->track;
    }

    public function getEntityType(): string
    {
        return Track::class;
    }

    public function getPersistenceType(): ChangeType
    {
        return ChangeType::UPDATED();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
