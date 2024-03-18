<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Modules\Library\Enum\MediaProvider;

class TrackVideoChanged extends TrackEvent
{
    public function __construct(
        public string $id,
        public ?string $url,
        public MediaProvider $provider = MediaProvider::YouTube,
    ) {}
}
