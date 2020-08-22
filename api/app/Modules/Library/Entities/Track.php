<?php

declare(strict_types=1);

namespace App\Modules\Library\Entities;

use App\Modules\Lyrics\Documents\Document;

class Track
{
    public string $id;
    public string $title;
    public ?Document $lyrics;
    public ?string $audio;
}
