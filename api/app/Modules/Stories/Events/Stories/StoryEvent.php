<?php

declare(strict_types=1);

namespace App\Modules\Stories\Events\Stories;

use App\Modules\Core\Events\UserAction;

abstract class StoryEvent extends UserAction
{
    public string $id;
}
