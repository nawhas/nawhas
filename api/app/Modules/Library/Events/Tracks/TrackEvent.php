<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Modules\Library\Events\UserAction;

abstract class TrackEvent extends UserAction
{
    public string $id;
}
