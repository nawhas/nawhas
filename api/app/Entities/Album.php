<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\Entity;
use App\Entities\Contracts\TimestampedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Album implements Entity, TimestampedEntity
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getArtwork(): ?string
    {
        return $this->artwork;
    }

    /**
     * @return Collection|Track[]
     */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }
}
