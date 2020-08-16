<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Core\Events\UserAction;

class AlbumArtworkChanged extends UserAction
{
    public string $id;
    public ?string $artwork;

    public function __construct(string $id, ?string $artwork)
    {
        $this->id = $id;
        $this->artwork = $artwork;
    }
}
