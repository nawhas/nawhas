<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Lyrics;

use Spatie\EventSourcing\ShouldBeStored;

class LyricsDeleted implements ShouldBeStored
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
