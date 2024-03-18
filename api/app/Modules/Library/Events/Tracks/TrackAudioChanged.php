<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

class TrackAudioChanged extends TrackEvent
{
    public function __construct(
        public string $id,
        public ?string $path
    ) {}
}
