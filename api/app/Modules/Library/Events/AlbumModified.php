<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Entities\Album;
use App\Entities\Contracts\Events\EntityPersisted;
use App\Enum\ChangeType;
use App\Modules\Audit\Events\AuditableEvent;

class AlbumModified implements EntityPersisted, AuditableEvent
{
    public Album $album;

    public function __construct(Album $album)
    {
        $this->album = $album;
    }

    public function getEntity(): Album
    {
        return $this->album;
    }

    public function getEntityType(): string
    {
        return Album::class;
    }

    public function getChangeType(): ChangeType
    {
        return ChangeType::UPDATED();
    }
}
