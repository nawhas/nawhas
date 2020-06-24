<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Reciter;
use App\Entities\User;
use App\Enum\PersistenceType;
use App\Modules\Audit\Events\AuditableEvent;

class ReciterModified implements EntityPersisted, AuditableEvent
{
    public Reciter $reciter;
    public User $user;

    public function __construct(Reciter $reciter, User $user)
    {
        $this->reciter = $reciter;
        $this->user = $user;
    }

    public function getEntity(): Reciter
    {
        return $this->reciter;
    }

    public function getEntityType(): string
    {
        return Reciter::class;
    }

    public function getPersistenceType(): PersistenceType
    {
        return PersistenceType::UPDATE();
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
