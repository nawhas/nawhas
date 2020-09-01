<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

abstract class UserAction extends ShouldBeStored implements UserAwareEvent
{
    use HasUserContext;
}
