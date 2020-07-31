<?php

declare(strict_types=1);

namespace App\Modules\Core\Contracts;

use DateTimeInterface;

interface TimestampedEntity
{
    public function getCreatedAt(): ?DateTimeInterface;
    public function getUpdatedAt(): ?DateTimeInterface;
}
