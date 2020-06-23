<?php

declare(strict_types=1);

namespace App\Entities\Contracts\Events;

use App\Entities\Contracts\Entity;
use App\Enum\PersistenceType;

interface EntityPersisted
{
    public function getEntity(): Entity;
    public function getEntityType(): string;
    public function getPersistenceType(): PersistenceType;
}
