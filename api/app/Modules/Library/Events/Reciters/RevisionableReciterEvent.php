<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Audit\Events\RevisionableEvent;
use App\Modules\Audit\Models\Revisionable;
use App\Modules\Library\Events\UserAction;
use App\Modules\Library\Models\Reciter;

abstract class RevisionableReciterEvent extends UserAction implements RevisionableEvent
{
    public string $id;

    public function revisionable(): Revisionable
    {
        return Reciter::retrieve($this->id);
    }
}
