<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;
use Illuminate\Support\Collection;

class ReciterSnapshot implements Snapshot
{
    public string $id;
    public string $name;
    public ?string $description;
    public ?string $avatar;

    /**
     * Collection of Album IDs.
     * @var Collection|string[]
     */
    public Collection $albums;

    public function __construct(
        string $id,
        string $name,
        ?string $description = null,
        ?string $avatar = null,
        array $albumIds = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->avatar = $avatar;
        $this->albums = collect($albumIds);
    }

    public static function fromArray(array $attributes): static
    {
        return new self(
            $attributes['id'],
            $attributes['name'],
            $attributes['description'],
            $attributes['avatar'],
            $attributes['albums'],
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
            'name'  => $this->name,
            'description' => $this->description,
            'avatar' => $this->avatar,
            'albums' => $this->albums->all(),
        ];
    }

    public function getType(): EntityType
    {
        return EntityType::RECITER();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
