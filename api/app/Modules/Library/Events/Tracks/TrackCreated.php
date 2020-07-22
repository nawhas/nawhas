<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use Spatie\EventSourcing\ShouldBeStored;

class TrackCreated implements ShouldBeStored
{
    public string $id;
    public string $albumId;
    public array $attributes = [];

    public function __construct(string $id, string $albumId, array $attributes)
    {
        $this->id = $id;
        $this->albumId = $albumId;
        $this->attributes = $attributes;
    }
}
