<?php

declare(strict_types=1);

namespace App\Entities\Contracts;

use Carbon\Carbon;

interface TimestampedEntity
{
    public function getCreatedAt(): ?Carbon;
    public function getUpdatedAt(): ?Carbon;
}
