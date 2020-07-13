<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events;

use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditableEntity;

interface AuditableEvent
{
    public function getEntity(): AuditableEntity;
    public function getChangeType(): ChangeType;
}
