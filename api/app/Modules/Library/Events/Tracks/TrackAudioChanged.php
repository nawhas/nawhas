<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

class TrackAudioChanged extends TrackEvent
{
    public ?string $path;

    public function __construct(string $id, ?string $path)
    {
        $this->id = $id;
        $this->path = $path;
    }
}
