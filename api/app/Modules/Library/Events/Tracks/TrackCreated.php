<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

class TrackCreated extends TrackEvent
{
    public string $albumId;
    public array $attributes = [];

    public function __construct(string $id, string $albumId, array $attributes)
    {
        $this->id = $id;
        $this->albumId = $albumId;
        $this->attributes = $attributes;
    }
}
