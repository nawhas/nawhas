<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use Spatie\EventSourcing\EventSerializers\EventSerializer as SpatieEventSerializer;
use Spatie\EventSourcing\EventSerializers\JsonEventSerializer;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EventSerializer implements SpatieEventSerializer
{
    private JsonEventSerializer $serializer;

    public function __construct(JsonEventSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize(ShouldBeStored $event): string
    {
        if ($event instanceof SerializableEvent) {
            return json_encode($event->toPayload());
        }

        return $this->serializer->serialize($event);
    }

    public function deserialize(string $eventClass, string $json, string $metadata = null): ShouldBeStored
    {
        if (is_subclass_of($eventClass, SerializableEvent::class)) {
            $event = $eventClass::fromPayload(json_decode($json, true));

            if (!($event instanceof ShouldBeStored)) {
                throw new \RuntimeException('Event must be a subclass of ' . ShouldBeStored::class . '.');
            }

            return $event;
        }

        return $this->serializer->deserialize($eventClass, $json, $metadata);
    }
}
