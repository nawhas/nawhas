<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Topics;

use App\Modules\Core\Events\UserAction;

abstract class TopicEvent extends UserAction
{
    public string $id;
}
