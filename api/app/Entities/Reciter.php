<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\Entity;
use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Audit\Entities\AuditableEntity;
use App\Visits\Visitable;
use App\Visits\Entities\ReciterVisit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Reciter implements Entity, TimestampedEntity, Visitable, AuditableEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private string $name;
    private string $slug;
    private ?string $description;
    private ?string $avatar;

    /** @var Collection|ReciterVisit[] */
    private Collection $visits;

    public function __construct(string $name, ?string $description = null, ?string $avatar = null)
    {
        $this->id = Uuid::uuid1();
        $this->name = $name;
        $this->slug = Str::slug($name);
        $this->description = $description;
        $this->avatar = $avatar;
        $this->visits = new ArrayCollection();
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

    public function getAvatarUrl(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function setAvatar(string $path): void
    {
        $this->avatar = $path;
    }

    public function getUrlPath(): string
    {
        return "/reciters/{$this->getSlug()}";
    }

    public function visit(): ReciterVisit
    {
        return new ReciterVisit($this);
    }

    public function getTrackedFields(): array
    {
        return [
            'name',
            'description',
            'avatar',
        ];
    }
}
