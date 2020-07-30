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
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent afterVersion($version)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Core\Events\StoredEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Core\Events\StoredEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Core\Events\StoredEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent startingFrom($storedEventId)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent uuid($uuid)
 * @method static \Illuminate\Database\Eloquent\Builder|\Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent withMetaDataAttributes()
 * @mixin \Eloquent
 */
class StoredEvent extends EloquentStoredEvent
{
    protected $connection = 'events';
}
