<?php

declare(strict_types=1);

namespace App\Modules\Audit\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\Entity;
use App\Entities\Contracts\TimestampedEntity;
use App\Entities\User;
use App\Enum\ChangeType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class AuditRecord implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private ChangeType $type;
    private User $user;
    private string $entity;
    private string $entityId;
    private ?array $old;
    private ?array $new;

    public function __construct(ChangeType $changeType, User $user, string $entity, string $entityId, ?array $old, ?array $new)
    {
        $this->id = Uuid::uuid1();
        $this->type = $changeType;
        $this->user = $user;
        $this->entity = $entity;
        $this->entityId = $entityId;
        $this->old = $old;
        $this->new = $new;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getType(): ChangeType
    {
        return $this->type;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }
    public function getEntityId(): string
    {
        return $this->entityId;
    }
    public function getOld(): ?array
    {
        return $this->old;
    }
    public function getNew(): ?array
    {
        return $this->new;
    }
}
