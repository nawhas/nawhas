<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;
use Illuminate\Support\Collection;

class ReciterSnapshot implements Snapshot
{
    /**
     * Collection of Album IDs.
     * @var Collection|string[]
     */
    public Collection $albums;

    public function __construct(
        public string $id,
        public string $name,
        public ?string $description = null,
        public ?string $avatar = null,
        array $albumIds = []
    ) {
        $this->albums = collect($albumIds);
    }

    public static function fromArray(array $data): static
    {
        return new static(
            $data['id'],
            $data['name'],
            $data['description'],
            $data['avatar'],
            $data['albums'],
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
            'name'  => $this->name,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'albums' => $this->albums->all(),
        ];
    }

    public function getType(): EntityType
    {
        return EntityType::Reciter;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
