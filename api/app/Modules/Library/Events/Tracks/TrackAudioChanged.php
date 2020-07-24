<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use Spatie\EventSourcing\ShouldBeStored;

class TrackAudioChanged implements ShouldBeStored
{
    public string $id;
    public ?string $path;

    public function __construct(string $id, ?string $path)
    {
        $this->id = $id;
        $this->path = $path;
    }
}
