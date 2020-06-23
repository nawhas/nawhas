<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\PersistenceType;

class ReciterCreated extends ReciterModified
{
    public function getPersistenceType(): PersistenceType
    {
        return PersistenceType::CREATE();
    }
}
