<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PasswordResetRequested extends ShouldBeStored
{
    public string $userId;
    public string $token;

    public function __construct(string $userId, string $token)
    {
        $this->userId = $userId;
        $this->token = $token;
    }
}
