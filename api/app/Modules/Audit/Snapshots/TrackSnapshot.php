<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;
use App\Modules\Lyrics\Documents\{Document, Factory, Format};

class TrackSnapshot implements Snapshot
{
    public function __construct(
        public string $id,
        public string $albumId,
        public string $title,
        public ?string $audio = null,
        public ?string $video = null,
        public ?Document $lyrics = null
    ) {}

    public static function fromArray(array $data): static
    {
        $lyrics = $data['lyrics'] ?? null;

        if ($lyrics) {
            $lyrics = Factory::create($lyrics['content'], Format::from($lyrics['format']));
        }

        return new static(
            $data['id'],
            $data['albumId'],
            $data['title'],
            $data['audio'] ?? null,
            $data['video'] ?? null,
            $lyrics,
        );
    }

    public static function fromRevision(Revision $revision): static
    {
        return static::fromArray($revision->new_values);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'albumId' => $this->albumId,
            'title' => $this->title,
            'audio' => $this->audio,
            'video' => $this->video,
            'lyrics' => optional($this->lyrics)->toArray(),
        ];
    }

    public function getType(): EntityType
    {
        return EntityType::TRACK();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
