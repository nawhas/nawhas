<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Playlists;

use App\Modules\Core\Events\HasUserContext;
use App\Modules\Core\Events\SerializableEvent;
use App\Modules\Core\Events\UserAwareEvent;
use App\Modules\Library\Events\UserAction;

class PlaylistNameChanged extends UserAction implements SerializableEvent, UserAwareEvent
{
    use HasUserContext;

    public string $id;
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public static function fromPayload(array $payload): SerializableEvent
    {
        return new self($payload['id'], $payload['name']);
    }
}
