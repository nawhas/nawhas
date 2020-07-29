<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Modules\Core\Events\SerializableEvent;
use Carbon\Carbon;
use DateTimeInterface;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

abstract class EntityViewed extends ShouldBeStored implements SerializableEvent
{
    public string $id;
    public DateTimeInterface $visitedAt;

    public function __construct(string $id, ?DateTimeInterface $visitedAt = null)
    {
        $this->id = $id;
        $this->visitedAt = $visitedAt ?? now();
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'visited_at' => $this->visitedAt->format(DATE_RFC3339),
        ];
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new static($payload['id'], Carbon::parse($payload['visited_at']));
    }
}
