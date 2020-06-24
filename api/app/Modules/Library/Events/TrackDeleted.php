<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\ChangeType;

class TrackDeleted extends TrackModified
{
    public function getChangeType(): ChangeType
    {
        return ChangeType::DELETED();
    }
}
