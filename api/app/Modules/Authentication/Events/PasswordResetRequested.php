<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PasswordResetRequested extends ShouldBeStored
{
    public function __construct(
        public string $userId,
        public string $token
    ) {}
}
