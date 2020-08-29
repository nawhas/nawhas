<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Modules\Core\Events\SerializableEvent;
use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;

class TrackLyricsChanged extends TrackEvent implements SerializableEvent
{
    public ?Document $document;

    public function __construct(string $id, ?Document $document)
    {
        $this->id = $id;
        $this->document = $document;
    }

    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'document' => $this->document ? $this->document->toArray() : null,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        $document = $payload['document'] ? Factory::create(
            $payload['document']['content'],
            new Format($payload['document']['format'])
        ) : null;

        return new static($payload['id'], $document);
    }
}
