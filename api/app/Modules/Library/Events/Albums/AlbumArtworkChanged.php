<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use Spatie\EventSourcing\ShouldBeStored;

class AlbumArtworkChanged implements ShouldBeStored
{
    public string $id;
    public ?string $artwork;

    public function __construct(string $id, ?string $artwork)
    {
        $this->id = $id;
        $this->artwork = $artwork;
    }
}
