<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Reciter implements Entity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private string $name;
    private ?string $description = null;
    private ?string $avatar = null;

    public function __construct(string $name, ?string $description = null, ?string $avatar = null)
    {
        $this->id = Uuid::uuid1();
        $this->name = $name;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }
}
