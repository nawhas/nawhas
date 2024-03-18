<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

class AlbumArtworkChanged extends AlbumEvent
{
    public function __construct(
        public string $id,
        public ?string $artwork
    ) {}
}
