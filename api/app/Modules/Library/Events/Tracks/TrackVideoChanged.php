<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Enum\MediaProvider;

class TrackVideoChanged extends TrackEvent
{
    public ?string $url;
    public string $provider = MediaProvider::YOUTUBE;

    public function __construct(string $id, ?string $url)
    {
        $this->id = $id;
        $this->url = $url;
    }
}
