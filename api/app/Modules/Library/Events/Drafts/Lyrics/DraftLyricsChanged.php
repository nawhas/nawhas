<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Drafts\Lyrics;

use App\Modules\Core\Events\SerializableEvent;
use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;

class DraftLyricsChanged extends DraftLyricsEvent implements SerializableEvent
{
    public function __construct(
        public string $id,
        public Document $document
    ) {}

    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'document' => $this->document->toArray(),
        ];
    }

    /**
     * @throws \JsonException
     */
    public static function fromPayload(array $payload): static
    {
        $format = Format::from($payload['document']['format']);
        $content = $payload['document']['content'];
        $document = Factory::create($content, $format);

        return new static($payload['id'], $document);
    }
}
