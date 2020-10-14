<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Audit\Models\Revision;

class TopicSnapshot implements Snapshot
{
    public string $id;
    public string $name;
    public ?string $description;
    public ?string $image;

    public function __construct(
        string $id,
        string $name,
        ?string $description = null,
        ?string $image = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['description'],
            $data['image'],
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
            'name'  => $this->name,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }

    public function getType(): EntityType
    {
        return EntityType::TOPIC();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
