<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

class AlbumArtworkChanged extends AlbumEvent
{
    public ?string $artwork;

    public function __construct(string $id, ?string $artwork)
    {
        $this->id = $id;
        $this->artwork = $artwork;
    }
}
