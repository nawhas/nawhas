<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

use App\Modules\Audit\Enum\EntityType;

interface Snapshot
{
    public static function fromArray(array $data): self;
    public function toArray(): array;
    public function getType(): EntityType;
    public function getId(): string;
}
