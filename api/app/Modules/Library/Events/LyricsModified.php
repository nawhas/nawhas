<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Lyrics;
use App\Enum\ChangeType;
use App\Modules\Audit\Entities\AuditableEntity;
use App\Modules\Audit\Events\AuditableEvent;
use App\Modules\Audit\Events\ChangeAwareAuditableEvent;

class LyricsModified implements EntityPersisted, AuditableEvent, ChangeAwareAuditableEvent
{
    public Lyrics $current;
    public Lyrics $previous;

    public function __construct(Lyrics $current, Lyrics $previous)
    {
        $this->current = $current;
        $this->previous = $previous;
    }

    public function getEntity(): Lyrics
    {
        return $this->current;
    }

    public function getEntityType(): string
    {
        return Lyrics::class;
    }

    public function getChangeType(): ChangeType
    {
        return ChangeType::UPDATED();
    }

    public function getPreviousEntity(): AuditableEntity
    {
        return $this->previous;
    }
}
