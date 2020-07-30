<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

trait HasUserContext
{
    protected ?string $userId = null;

    public function getUserId(): ?string
    {
        if ($this->userId !== null) {
            return $this->userId;
        }

        return auth()->id();
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }
}
