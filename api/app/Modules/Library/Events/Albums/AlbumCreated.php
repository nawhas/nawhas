<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Audit\Enum\ChangeType;

class AlbumCreated extends RevisionableAlbumEvent
{
    public string $reciterId;
    public array $attributes = [];

    public function __construct(string $id, string $reciterId, array $attributes)
    {
        $this->id = $id;
        $this->reciterId = $reciterId;
        $this->attributes = $attributes;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::CREATED();
    }
}
