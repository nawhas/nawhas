<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Modules\Core\Events\SerializableEvent;
use App\Modules\Core\Events\UserAction;
use Carbon\Carbon;
use DateTimeInterface;

abstract class EntityViewed extends UserAction implements SerializableEvent
{
    public function __construct(
        public string $id,
        public ?DateTimeInterface $visitedAt = new Carbon()
    ) {}

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
