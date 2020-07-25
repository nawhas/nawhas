<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

interface SerializableEvent
{
    public function toPayload(): array;

    public static function fromPayload(array $payload): self;
}
