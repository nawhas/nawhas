<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

interface UserAwareEvent
{
    public function getUserId(): ?string;
    public function setUserId(?string $id): static;
}
