<?php

declare(strict_types=1);

namespace App\Entities\Behaviors;

use Carbon\Carbon;
use DateTimeInterface;

trait HasTimestamps
{
    protected ?DateTimeInterface $createdAt = null;
    protected ?DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
