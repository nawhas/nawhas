<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Enum\MediaProvider;

class TrackVideoChanged extends TrackEvent
{
    public string $provider;

    public function __construct(
        public string $id,
        public ?string $url,
        MediaProvider $provider
    ) {
        $this->provider = $provider->value;
    }
}
