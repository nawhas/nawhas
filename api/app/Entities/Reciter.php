<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\Entity;
use App\Entities\Contracts\TimestampedEntity;
use Illuminate\Support\Str;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Reciter implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private string $name;
    private string $slug;
    private ?string $description = null;
    private ?string $avatar = null;

    public function __construct(string $name, ?string $description = null, ?string $avatar = null)
    {
        $this->id = Uuid::uuid1();
        $this->name = $name;
        $this->slug = Str::slug($name);
        $this->description = $description;
        $this->avatar = $avatar;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
        $this->slug = Str::slug($name);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function hasAvatar(): bool
    {
        return $this->avatar !== null;
    }

    public function setAvatar(string $path): void
    {
        $this->avatar = $path;
    }
}
