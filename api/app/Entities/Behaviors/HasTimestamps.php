<?php

declare(strict_types=1);

namespace App\Entities\Behaviors;

use Carbon\Carbon;
use DateTimeInterface;

trait HasTimestamps
{
    protected ?DateTimeInterface $createdAt = null;
    protected ?DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?Carbon
    {
        return Carbon::instance($this->createdAt);
    }

    public function getUpdatedAt(): ?Carbon
    {
        return Carbon::instance($this->updatedAt);
    }
}
