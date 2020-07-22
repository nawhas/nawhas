<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Lyrics;

use Spatie\EventSourcing\ShouldBeStored;

class LyricsChanged implements ShouldBeStored
{
    public string $id;
    public string $trackId;
    public string $content;
    public int $format;

    public function __construct(string $id, string $trackId, string $content, int $format)
    {
        $this->id = $id;
        $this->trackId = $trackId;
        $this->content = $content;
        $this->format = $format;
    }
}