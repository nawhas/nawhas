<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;
use Illuminate\Support\Collection;

class AlbumSnapshot implements Snapshot
{
    /**
     * @var Collection<string>
     */
    public Collection $tracks;

    public function __construct(
        public string $id,
        public string $reciterId,
        public string $title,
        public string $year,
        public ?string $artwork = null,
        array $trackIds = []
    ) {
        $this->tracks = collect($trackIds);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            $data['id'],
            $data['reciterId'],
            $data['title'],
            $data['year'],
            $data['artwork'],
            $data['tracks'],
        );
    }

    public static function fromRevision(Revision $revision): static
    {
        return self::fromArray($revision->new_values);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'reciterId' => $this->reciterId,
            'title' => $this->title,
            'year' => $this->year,
            'artwork' => $this->artwork,
            'tracks' => $this->tracks->all(),
        ];
    }

    public function getType(): EntityType
    {
        return EntityType::ALBUM();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
