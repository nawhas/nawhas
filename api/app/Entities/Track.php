<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Track implements Entity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Reciter $reciter;
    private Album $album;
    private string $title;

    public function __construct(Album $album, string $title)
    {
        $this->id = Uuid::uuid1();
        $this->album = $album;
        $this->reciter = $album->getReciter();
        $this->title = $title;
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
}
