<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\ChangeType;

class ReciterCreated extends ReciterModified
{
    public function getPersistenceType(): ChangeType
    {
        return ChangeType::CREATED();
    }
}
