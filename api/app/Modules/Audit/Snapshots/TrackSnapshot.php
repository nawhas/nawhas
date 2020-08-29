<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Models\Revision;
use App\Modules\Lyrics\Documents\{Document, Factory, Format};

class TrackSnapshot implements Snapshot
{
    public string $id;
    public string $albumId;
    public string $title;
    public ?Document $lyrics;
    public ?string $audio;

    public function __construct(
        string $id,
        string $albumId,
        string $title,
        ?string $audio = null,
        ?Document $lyrics = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->lyrics = $lyrics;
        $this->audio = $audio;
        $this->albumId = $albumId;
    }

    public static function fromArray(array $attributes): self
    {
        $lyrics = $attributes['lyrics'];

        if ($lyrics) {
            $lyrics = Factory::create($lyrics['content'], new Format($lyrics['format']));
        }

        return new self(
            $attributes['id'],
            $attributes['albumId'],
            $attributes['title'],
            $lyrics,
            $attributes['audio'],
        );
    }

    public static function fromRevision(Revision $revision): self
    {
        return self::fromArray($revision->new_values);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'albumId' => $this->albumId,
            'title' => $this->title,
            'audio' => $this->audio,
            'lyrics' => optional($this->lyrics)->toArray(),
        ];
    }

    public function getType(): string
    {
        return 'track';
    }

    public function getId(): string
    {
        return $this->id;
    }
}
