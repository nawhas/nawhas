<?php

declare(strict_types=1);

namespace App\Entities;

use App\Entities\Behaviors\HasTimestamps;
use App\Modules\Audit\Entities\AuditableEntity;
use App\Modules\Lyrics\Documents\Document;
use App\Modules\Lyrics\Documents\Factory;
use App\Modules\Lyrics\Documents\Format;
use App\Entities\Contracts\{Entity, TimestampedEntity};
use Ramsey\Uuid\{Uuid, UuidInterface};
use Zain\LaravelDoctrine\Jetpack\Serializer\SerializesAttributes;

class Lyrics implements Entity, TimestampedEntity, AuditableEntity
{
    use HasTimestamps;
    use SerializesAttributes;

    private UuidInterface $id;
    private Track $track;
    private string $content;
    private int $format;

    public function __construct(Track $track, string $content, Format $format)
    {
        $this->id = Uuid::uuid1();
        $this->track = $track;
        $this->content = $content;
        $this->format = $format->getValue();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getTrack(): Track
    {
        return $this->track;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDocument(): Document
    {
        return Factory::create($this->content, $this->getFormat());
    }

    public function getFormat(): Format
    {
        return new Format($this->format);
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'trackId' => $this->getTrack()->getId(),
            'content' => $this->getContent(),
            'format' => $this->getFormat()->getValue(),
        ];
    }

    public function getTrackedFields(): array
    {
        return ['format', 'content'];
    }
}
