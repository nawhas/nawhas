<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

class TrackCreated extends TrackEvent
{
    public function __construct(
        public string $id,
        public string $albumId,
        public array $attributes = []
    ) {}
}
