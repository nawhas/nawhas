<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Library\Events\UserAction;

class TrackDeleted extends TrackEvent
{
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::DELETED();
    }
}
