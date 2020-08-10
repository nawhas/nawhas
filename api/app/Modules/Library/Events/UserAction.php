<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Modules\Core\Events\HasUserContext;
use App\Modules\Core\Events\UserAwareEvent;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

abstract class UserAction extends ShouldBeStored implements UserAwareEvent
{
    use HasUserContext;
}
