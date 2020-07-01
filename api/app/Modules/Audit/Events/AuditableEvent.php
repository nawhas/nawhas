<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events;

use App\Entities\Contracts\Entity;
use App\Enum\ChangeType;

interface AuditableEvent
{
    public function getEntity(): Entity;
    public function getChangeType(): ChangeType;
}
