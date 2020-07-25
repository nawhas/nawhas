<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrackLyricsChanged extends ShouldBeStored
{
    public string $id;
    public string $content;
    public int $format;

    public function __construct(string $id, string $content, int $format)
    {
        $this->id = $id;
        $this->content = $content;
        $this->format = $format;
    }
}
