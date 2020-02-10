<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\TimestampedEntity;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Album implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Reciter $reciter;
    private int $year;
    private ?string $artwork = null;

    public function __construct(Reciter $reciter, int $year, ?string $artwork = null)
    {
        $this->id = Uuid::uuid1();
        $this->reciter = $reciter;
        $this->year = $year;
        $this->artwork = $artwork;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getReciter(): Reciter
    {
        return $this->reciter;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getArtwork(): ?string
    {
        return $this->artwork;
    }
}
