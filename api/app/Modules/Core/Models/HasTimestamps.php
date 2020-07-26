<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
trait HasTimestamps
{
    public function getCreatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->created_at);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->updated_at);
    }
}
