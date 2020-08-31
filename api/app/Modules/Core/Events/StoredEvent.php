<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

/**
 * App\Modules\Core\Events\StoredEvent
 *
 * @property int $id
 * @property string|null $aggregate_uuid
 * @property int|null $aggregate_version
 * @property string $event_class
 * @property array $event_properties
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes $meta_data
 * @property string $created_at
 * @property-read \Spatie\EventSourcing\StoredEvents\ShouldBeStored|null $event
 * @method static Builder|EloquentStoredEvent afterVersion($version)
 * @method static \Illuminate\Database\Eloquent\Builder|StoredEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoredEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StoredEvent query()
 * @method static Builder|EloquentStoredEvent startingFrom($storedEventId)
 * @method static Builder|EloquentStoredEvent uuid($uuid)
 * @method static Builder|EloquentStoredEvent withMetaDataAttributes()
 * @mixin \Eloquent
 */
class StoredEvent extends EloquentStoredEvent
{
    protected $connection = 'events';
}
