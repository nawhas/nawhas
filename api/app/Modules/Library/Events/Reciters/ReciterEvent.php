<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Library\Events\UserAction;

abstract class ReciterEvent extends UserAction
{
    public string $id;
}
