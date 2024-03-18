<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use Spatie\EventSourcing\EventSerializers\EventSerializer;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;
use Tests\TestCase;

class EnumNormalizerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_denormalize_events_with_string_backed_enums(): void
    {
        $event = new StringEnumEvent(StringEnum::Bar);
        $serializer = app(EventSerializer::class);

        $json = $serializer->serialize($event);

        /** @var \Tests\Unit\Events\StringEnumEvent $deserialized */
        $deserialized = $serializer->deserialize(StringEnumEvent::class, $json, version: 1);

        $this->assertSame($deserialized->bar, $event->bar);
    }

    /**
     * @test
     */
    public function it_can_denormalize_events_with_int_backed_enums(): void
    {
        $event = new IntEnumEvent(IntEnum::Foo);
        $serializer = app(EventSerializer::class);

        $json = $serializer->serialize($event);

        /** @var \Tests\Unit\Events\IntEnumEvent $deserialized */
        $deserialized = $serializer->deserialize(IntEnumEvent::class, $json, version: 1);

        $this->assertSame($deserialized->bar, $event->bar);
    }
}

enum StringEnum: string
{
    case Foo = 'foo';
    case Bar = 'bar';
}
class StringEnumEvent extends ShouldBeStored
{
    public function __construct(
        public StringEnum $bar,
    ) {}
}

enum IntEnum: int
{
    case Foo = 1;
    case Bar = 2;
}
class IntEnumEvent extends ShouldBeStored
{
    public function __construct(
        public IntEnum $bar,
    ) {}
}
