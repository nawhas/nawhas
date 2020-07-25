<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserRememberTokenChanged extends ShouldBeStored
{
    public string $id;
    public bool $rememberToken;

    public function __construct(string $id, bool $rememberToken)
    {
        $this->id = $id;
        $this->rememberToken = $rememberToken;
    }
}
