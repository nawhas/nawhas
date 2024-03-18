<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use App\Modules\Core\Events\StoredEvent as StoredEventModel;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

trait InteractsWithStoredEvents
{
    protected function getStoredEvent(ShouldBeStored $event): StoredEvent
    {
        /** @var StoredEventModel $stored */
        $stored = StoredEventModel::findOrFail($event->storedEventId());

        return $stored->toStoredEvent();
    }
}
