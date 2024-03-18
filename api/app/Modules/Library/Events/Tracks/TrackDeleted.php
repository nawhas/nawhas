<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

class TrackDeleted extends TrackEvent
{
    public function __construct(
        public string $id
    ) {}
}
