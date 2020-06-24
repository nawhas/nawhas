<?php

declare(strict_types=1);

namespace App\Modules\Audit\Entities;

use App\Entities\Contracts\Entity;

interface AuditableEntity extends Entity
{
    public function getTrackedFields(): array;
}
