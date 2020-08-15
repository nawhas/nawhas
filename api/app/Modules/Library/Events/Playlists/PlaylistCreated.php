<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Playlists;

use App\Modules\Core\Events\HasUserContext;
use App\Modules\Library\Events\UserAction;

class PlaylistCreated extends UserAction
{
    use HasUserContext;

    public string $id;
    public array $attributes = [];

    public function __construct(string $id, array $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }
}
