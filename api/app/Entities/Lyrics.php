<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Lyrics implements Entity, TimestampedEntity
{
    /**
     * Format 1:
     * Flat text document.
     */
    public const FORMAT_PLAIN_TEXT = 1;

    /**
     * Version 2:
     * JSON document:
     * [
     *   {
     *     "timestamp": int,
     *     "lines": [
     *       { "text": string, "repeat": optional<int> }
     *     ]
     *   }
     * ]
     */
    public const FORMAT_JSON_V1 = 2;

    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Track $track;
    private string $content;
    private int $format;

    public function __construct(Track $track, string $content, int $format)
    {
        $this->id = Uuid::uuid1();
        $this->track = $track;
        $this->content = $content;
        $this->format = $format;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getTrack(): Track
    {
        return $this->track;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFormat(): int
    {
        return $this->format;
    }
}
