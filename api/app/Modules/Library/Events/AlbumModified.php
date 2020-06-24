<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Album;
use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\User;
use App\Enum\ChangeType;
use App\Modules\Audit\Events\AuditableEvent;

class AlbumModified implements EntityPersisted, AuditableEvent
{
    public Album $album;
    public User $user;

    public function __construct(Album $album, User $user)
    {
        $this->album = $album;
        $this->user = $user;
    }

    public function getEntity(): Album
    {
        return $this->album;
    }

    public function getEntityType(): string
    {
        return Album::class;
    }

    public function getChangeType(): ChangeType
    {
        return ChangeType::UPDATED();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
