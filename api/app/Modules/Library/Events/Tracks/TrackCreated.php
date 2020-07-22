<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use Spatie\EventSourcing\ShouldBeStored;

class TrackCreated implements ShouldBeStored
{
    public string $id;
    public string $albumId;
    public string $title;

    public function __construct(string $id, string $albumId, string $title)
    {
        $this->id = $id;
        $this->albumId = $albumId;
        $this->title = $title;
    }
}
