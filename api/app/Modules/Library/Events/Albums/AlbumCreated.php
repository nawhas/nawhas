<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

class AlbumCreated extends AlbumEvent
{
    public function __construct(
        public string $id,
        public string $reciterId,
        public array $attributes = []
    ) {}
}
