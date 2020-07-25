<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrackViewed extends ShouldBeStored
{
    public string $id;
    public array $data;

    public function __construct(string $id, array $data = [])
    {
        $this->id = $id;
        $this->data = $data;
    }
}
