<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Core\Events\StoredEvent;
use Tests\Feature\FeatureTest;
use Tests\WithSimpleFaker;

abstract class EventsTest extends FeatureTest
{
    use WithSimpleFaker;

    protected function setUp(): void
    {
        parent::setUp();

        StoredEvent::truncate();
    }

    protected function replay(): void
    {
        $result = $this->withoutMockingConsoleOutput()
            ->artisan('event-sourcing:replay', ['-n' => true]);
        $this->assertEquals(0, $result);
    }

    protected function event(
        string $event,
        array $properties,
        int $eventVersion = 1,
        \DateTimeInterface $createdAt = null,
        ?int $aggregateVersion = null,
        ?string $aggregateUuid = null,
        array $metadata = [],
    ): StoredEvent {
        return StoredEvent::create([
            'event_version' => $eventVersion,
            'created_at' => $createdAt ?? now(),
            'event_properties' => $properties,
            'aggregate_version' => $aggregateVersion,
            'aggregate_uuid' => $aggregateUuid,
            'meta_data' => $metadata,
            'event_class' => $event,
        ]);
    }
}
