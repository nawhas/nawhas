<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events;

use App\Modules\Audit\Entities\AuditableEntity;

interface ChangeAwareAuditableEvent extends AuditableEvent
{
    public function getPreviousEntity(): AuditableEntity;
}
