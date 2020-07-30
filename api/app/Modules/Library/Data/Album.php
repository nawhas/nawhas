<?php

declare(strict_types=1);

namespace App\Modules\Library\Data;

use App\Entities\Contracts\{Entity, TimestampedEntity};

interface Album extends Entity, TimestampedEntity
{
    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getYear(): string;

    public function setYear(string $year): void;

    public function getArtwork(): ?string;

    public function setArtwork(?string $artwork): void;
}
