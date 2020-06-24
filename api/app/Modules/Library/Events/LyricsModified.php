<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Lyrics;
use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Events\AuditableEvent;

class LyricsModified implements EntityPersisted, AuditableEvent
{
    public Lyrics $lyrics;
    public User $user;

    public function __construct(Lyrics $lyrics, User $user)
    {
        $this->lyrics = $lyrics;
        $this->user = $user;
    }

    public function getEntity(): Lyrics
    {
        return $this->lyrics;
    }

    public function getEntityType(): string
    {
        return Lyrics::class;
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
