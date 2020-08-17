<?php

declare(strict_types=1);

namespace App\Modules\Audit\Events;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Audit\Models\Revisionable;
use App\Modules\Core\Events\UserAwareEvent;

interface RevisionableEvent extends UserAwareEvent
{
    public function changeType(): ChangeType;
    public function revisionable(): Revisionable;
}
