<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\Entity;
use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Audit\Entities\AuditableEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Album implements Entity, TimestampedEntity, AuditableEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Reciter $reciter;
    private string $title;
    private string $year;
    private Collection $tracks;
    private ?string $artwork = null;

    public function __construct(Reciter $reciter, string $title, string $year, ?string $artwork = null)
    {
        $this->id = Uuid::uuid1();
        $this->reciter = $reciter;
        $this->title = $title;
        $this->year = $year;
        $this->artwork = $artwork;
        $this->tracks = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getReciter(): Reciter
    {
        return $this->reciter;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): void
    {
        $this->year = $year;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getArtwork(): ?string
    {
        return $this->artwork;
    }

    public function getArtworkUrl(): ?string
    {
        return $this->artwork ? Storage::url($this->artwork) : null;
    }

    public function setArtwork(string $artwork): void
    {
        $this->artwork = $artwork;
    }

    public function hasArtwork(): bool
    {
        return $this->getArtwork() !== null;
    }

    /**
     * @return Collection|Track[]
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function getUrlPath(): string
    {
        return "{$this->getReciter()->getUrlPath()}/albums/{$this->getYear()}";
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'reciterId' => $this->getReciter()->getId(),
            'title' => $this->getTitle(),
            'year' => $this->getYear(),
            'artwork' => $this->getArtwork(),
        ];
    }

    public function getTrackedFields(): array
    {
        return [
            'title',
            'year',
            'artwork',
        ];
    }
}
