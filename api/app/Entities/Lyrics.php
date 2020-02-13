<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Lyrics implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Track $track;
    private string $content;

    public function __construct(Track $track, string $content)
    {
        $this->id = Uuid::uuid1();
        $this->track = $track;
        $this->content = $content;
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
}
