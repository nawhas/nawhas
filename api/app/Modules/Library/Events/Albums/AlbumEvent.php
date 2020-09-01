<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Albums;

use App\Modules\Core\Events\UserAction;

abstract class AlbumEvent extends UserAction
{
    public string $id;
}
