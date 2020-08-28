<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use App\Modules\Authentication\Models\User;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use Auth;
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
            $payload = json_encode($event->toPayload());
        } else {
            $payload = $this->serializer->serialize($event);
        }

        if ($event instanceof UserAwareEvent) {
            $data = json_decode($payload, true);
            $data['userId'] = $event->getUserId();
            $payload = json_encode($data);
        }

        return $payload;
    }

    public function deserialize(string $eventClass, string $json, string $metadata = null): ShouldBeStored
    {
        if (is_subclass_of($eventClass, SerializableEvent::class)) {
            $payload = json_decode($json, true);
            $event = $eventClass::fromPayload($payload);

            if (!($event instanceof ShouldBeStored)) {
                throw new \RuntimeException('Event must be a subclass of ' . ShouldBeStored::class . '.');
            }

            if ($event instanceof UserAwareEvent) {
                $event->setUserId($payload['userId']);

                Auth::guard('events')->setUser(
                    $payload['userId'] ? User::retrieve($payload['userId']) : null
                );
            }

            return $event;
        }

        return $this->serializer->deserialize($eventClass, $json, $metadata);
    }
}
