<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Audit\Enum\ChangeType;

class AlbumDeleted extends RevisionableAlbumEvent
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
