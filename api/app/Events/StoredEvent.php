<?php

declare(strict_types=1);

namespace App\Events;

use Spatie\EventSourcing\Models\EloquentStoredEvent;

class StoredEvent extends EloquentStoredEvent
{
    protected $connection = 'events';
}
