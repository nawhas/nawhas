<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Enum\MediaProvider;

class TrackVideoChanged extends TrackEvent
{
    public ?string $videoId;
    public string $provider = MediaProvider::YOUTUBE;

    public function __construct(string $id, ?string $videoId)
    {
        $this->id = $id;
        $this->videoId = $videoId;
    }
}
