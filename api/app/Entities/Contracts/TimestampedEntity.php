<?php

declare(strict_types=1);

namespace App\Entities\Contracts;

use DateTimeInterface;

interface TimestampedEntity
{
    public function getCreatedAt(): ?DateTimeInterface;
    public function getUpdatedAt(): ?DateTimeInterface;
}
