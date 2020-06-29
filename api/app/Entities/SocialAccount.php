<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\Entity;
use App\Entities\Contracts\TimestampedEntity;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class SocialAccount implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private User $user;
    private string $provider;
    private string $providerId;

    public function __construct(User $user, string $provider, string $providerId)
    {
        $this->id = Uuid::uuid1();
        $this->user = $user;
        $this->provider = $provider;
        $this->providerId = $providerId;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getproviderId(): string
    {
        return $this->providerId;
    }

    public function setproviderId(string $providerId): void
    {
        $this->providerId = $providerId;
    }
}
