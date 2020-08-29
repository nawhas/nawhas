<?php

declare(strict_types=1);

namespace App\Modules\Audit\Snapshots;

interface Snapshot
{
    public static function fromArray(array $data): self;
    public function toArray(): array;
    public function getType(): string;
    public function getId(): string;
}
