<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AlbumArtworkChanged extends ShouldBeStored
{
    public string $id;
    public ?string $artwork;

    public function __construct(string $id, ?string $artwork)
    {
        $this->id = $id;
        $this->artwork = $artwork;
    }
}
