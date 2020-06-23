<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events;

use App\Entities\Contracts\Entity;

interface AuditableEvent
{
    public function getEntity(): Entity;
}
