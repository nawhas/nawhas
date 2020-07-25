<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserNameChanged extends ShouldBeStored
{
    public string $id;
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
