<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Enum\MediaProvider;
use App\Enum\MediaType;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Media implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private MediaType $type;
    private MediaProvider $provider;
    private string $uri;

    public function __construct(MediaType $type, MediaProvider $provider, string $uri)
    {
        $this->id = Uuid::uuid1();
        $this->type = $type;
        $this->provider = $provider;
        $this->uri = $uri;
    }

    public static function audioFile(string $uri): self
    {
        return new self(MediaType::AUDIO(), MediaProvider::FILE(), $uri);
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getType(): MediaType
    {
        return $this->type;
    }

    public function getProvider(): MediaProvider
    {
        return $this->provider;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
