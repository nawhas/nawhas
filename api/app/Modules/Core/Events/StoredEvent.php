<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use Carbon\CarbonImmutable;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;
use Spatie\EventSourcing\StoredEvents\StoredEvent as SpatieStoredEvent;

/**
 * App\Modules\Core\Events\StoredEvent
 *
 * @property int $id
 * @property string|null $aggregate_uuid
 * @property int|null $aggregate_version
 * @property int|null $event_version
 * @property string $event_class
 * @property array $event_properties
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes $meta_data
 * @property string $created_at
 * @property-read \Spatie\EventSourcing\StoredEvents\ShouldBeStored|null $event
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|EloquentStoredEvent afterVersion($version)
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|StoredEvent newModelQuery()
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|StoredEvent newQuery()
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|StoredEvent query()
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|EloquentStoredEvent startingFrom($storedEventId)
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|EloquentStoredEvent uuid($uuid)
 * @method static \Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEventQueryBuilder|EloquentStoredEvent withMetaDataAttributes()
 * @mixin \Eloquent
 */
class StoredEvent extends EloquentStoredEvent
{
    public function getConnectionName(): string
    {
        return 'events';
    }

    public function toStoredEvent(): SpatieStoredEvent
    {
        $stored = parent::toStoredEvent();

        // For older stored events, we don't have the created at or stored event ID
        // stored in the meta_data column in the DB. This enables seamless support
        // for replaying those old events.
        $stored->event->setCreatedAt(CarbonImmutable::make($this->created_at));
        $stored->event->setStoredEventId($this->id);

        return $stored;
    }
}
