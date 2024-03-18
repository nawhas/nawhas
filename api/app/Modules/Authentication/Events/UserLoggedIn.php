<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserLoggedIn extends ShouldBeStored
{
    public function __construct(
        public string $id
    ) {}
}
