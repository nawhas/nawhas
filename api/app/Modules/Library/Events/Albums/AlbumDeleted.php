<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

class AlbumDeleted extends AlbumEvent
{
    public function __construct(
        public string $id
    ) {}
}
