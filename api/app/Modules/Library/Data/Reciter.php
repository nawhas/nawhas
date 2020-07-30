<?php

declare(strict_types=1);

namespace App\Modules\Library\Data;

use App\Entities\Contracts\{Entity, TimestampedEntity};

interface Reciter extends Entity, TimestampedEntity
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getAvatar(): ?string;

    public function setAvatar(?string $avatar): void;
}
