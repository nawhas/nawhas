<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserRememberTokenChanged extends ShouldBeStored
{
    public string $id;
    public string $rememberToken;

    public function __construct(string $id, string $rememberToken)
    {
        $this->id = $id;
        $this->rememberToken = $rememberToken;
    }
}
