<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Reciter;
use App\Enum\ChangeType;
use App\Modules\Audit\Events\AuditableEvent;

class ReciterModified implements EntityPersisted, AuditableEvent
{
    public Reciter $reciter;

    public function __construct(Reciter $reciter)
    {
        $this->reciter = $reciter;
    }

    public function getEntity(): Reciter
    {
        return $this->reciter;
    }

    public function getEntityType(): string
    {
        return Reciter::class;
    }

    public function getChangeType(): ChangeType
    {
        return ChangeType::UPDATED();
    }
}
