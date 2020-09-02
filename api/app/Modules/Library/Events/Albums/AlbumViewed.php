<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Core\Events\UserAction;

class AlbumViewed extends UserAction
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
