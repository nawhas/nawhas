<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Enum\MediaProvider;
use App\Enum\MediaType;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Ramsey\Uuid\{Uuid, UuidInterface};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Media implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private MediaType $type;
    private MediaProvider $provider;
    private string $path;

    public function __construct(MediaType $type, MediaProvider $provider, string $path)
    {
        $this->id = Uuid::uuid1();
        $this->type = $type;
        $this->provider = $provider;
        $this->path = $path;
    }

    public static function audioFile(string $path): self
    {
        return new self(MediaType::AUDIO(), MediaProvider::FILE(), $path);
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getUri(): string
    {
        return Storage::url($this->path);
    }
}
