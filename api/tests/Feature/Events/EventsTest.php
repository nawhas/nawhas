<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Core\Events\StoredEvent;
use ReflectionAttribute;
use ReflectionMethod;
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
        $method = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]['function'];

        $this->verifyCoversEventAttribute($method, $event, $eventVersion);

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

    private function verifyCoversEventAttribute(string $method, string $event, int $version): void
    {
        $reflection = new ReflectionMethod($this, $method);
        $attributes = collect($reflection->getAttributes());

        assert(
            $attributes->map(fn (ReflectionAttribute $a) => $a->getName())->contains(CoversEvent::class),
            'An events test method MUST specify which events are covered by the test '
            . 'using the Tests\Feature\Events\CoversEvent attribute.',
        );

        assert(
            $attributes->map(fn (ReflectionAttribute $a) => $a->newInstance())
                ->map(fn (CoversEvent $c) => [$c->event, $c->version])
                ->contains([$event, $version]),
            sprintf(
                'The event %s (v%d) is not specified by the Tests\Feature\Events\CoversEvent attribute.',
                $event,
                $version
            )
        );
    }
}
