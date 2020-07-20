<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Contracts\Events\EntityPersisted;
use App\Entities\Lyrics;
use App\Enum\ChangeType;
use App\Modules\Audit\Events\AuditableEvent;

class LyricsDeleted implements EntityPersisted, AuditableEvent
{
    public Lyrics $lyrics;

    public function __construct(Lyrics $lyrics)
    {
        $this->lyrics = $lyrics;
    }

    public function getEntity(): Lyrics
    {
        return $this->lyrics;
    }

    public function getEntityType(): string
    {
        return Lyrics::class;
    }

    public function getChangeType(): ChangeType
    {
        return ChangeType::DELETED();
    }
}
