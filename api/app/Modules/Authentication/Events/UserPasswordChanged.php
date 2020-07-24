<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\ShouldBeStored;

class UserPasswordChanged implements ShouldBeStored
{
    public string $id;
    public string $password;

    public function __construct(string $id, string $password)
    {
        $this->id = $id;
        $this->password = $password;
    }
}
