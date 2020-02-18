<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Media implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    public const TYPE_AUDIO_FILE = 'audio-file';

    private UuidInterface $id;
    private string $type;
    private string $uri;

    public function __construct(string $type, string $uri)
    {
        $this->id = Uuid::uuid1();
        $this->type = $type;
        $this->uri = $uri;
    }

    public static function audioFile(string $uri): self
    {
        return new self(self::TYPE_AUDIO_FILE, $uri);
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
