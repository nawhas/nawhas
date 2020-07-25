<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

class StoredEvent extends EloquentStoredEvent
{
    protected $connection = 'events';
}
