<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserLoggedIn extends ShouldBeStored
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
