<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Illuminate\Support\Str;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Track implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Album $album;
    private string $title;
    private string $slug;
    private ?Lyrics $lyrics = null;

    public function __construct(Album $album, string $title)
    {
        $this->id = Uuid::uuid1();
        $this->album = $album;
        $this->title = $title;
        // TODO - This may need to take uniqueness into account?
        $this->slug = Str::slug($title);
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getReciter(): Reciter
    {
        return $this->getAlbum()->getReciter();
    }

    public function getAlbum(): Album
    {
        return $this->album;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLyrics(): ?Lyrics
    {
        return $this->lyrics;
    }

    public function replaceLyrics(Lyrics $lyrics): void
    {
        $this->lyrics = $lyrics;
    }
}
