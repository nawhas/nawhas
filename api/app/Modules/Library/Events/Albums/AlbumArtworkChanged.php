<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Audit\Enum\ChangeType;

class AlbumArtworkChanged extends RevisionableAlbumEvent
{
    public ?string $artwork;

    public function __construct(string $id, ?string $artwork)
    {
        $this->id = $id;
        $this->artwork = $artwork;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
