<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Enum\MediaProvider;
use App\Enum\MediaType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Illuminate\Support\Str;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Track implements Entity, TimestampedEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Reciter $reciter;
    private Album $album;
    private string $title;
    private string $slug;
    private ?Lyrics $lyrics = null;
    private Collection $media;

    public function __construct(Album $album, string $title)
    {
        $this->id = Uuid::uuid1();
        $this->album = $album;
        $this->reciter = $album->getReciter();
        $this->title = $title;
        $this->slug = Str::slug($title);
        $this->media = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getReciter(): Reciter
    {
        return $this->reciter;
    }

    public function getAlbum(): Album
    {
        return $this->album;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->slug = Str::slug($title);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLyrics(): ?Lyrics
    {
        return $this->lyrics;
    }

    public function hasLyrics(): bool
    {
        return $this->lyrics !== null;
    }

    public function replaceLyrics(Lyrics $lyrics): void
    {
        $this->lyrics = $lyrics;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addAudioFile(Media $media): void
    {
        $existing = $this->getAudioFile();

        if ($existing) {
            // Remove existing audio files.
            $this->media->removeElement($existing);
        }

        $this->media->add($media);
    }

    public function getAudioFile(): ?Media
    {
        if (!($this->media instanceof Selectable)) {
            throw new \BadMethodCallException('Cannot select audio files from a collection not implementing Selectable.');
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('type', MediaType::AUDIO()))
            ->where(Criteria::expr()->eq('provider', MediaProvider::FILE()));

        return $this->media->matching($criteria)->first();
    }

    public function hasAudioFile(): bool
    {
        return $this->getAudioFile() !== null;
    }
}
