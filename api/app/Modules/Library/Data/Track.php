<?php

declare(strict_types=1);

namespace App\Modules\Library\Data;

use App\Modules\Lyrics\Documents\Document;

class Track
{
    public string $id;
    public string $title;
    public ?Document $lyrics;
    public ?string $audio;

    public function __construct(string $id, string $title, ?Document $lyrics = null, ?string $audio = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->lyrics = $lyrics;
        $this->audio = $audio;
    }
}
