<?php

declare(strict_types=1);

namespace App\Modules\Core\Events;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard as GuardContract;

class Guard implements GuardContract
{
    private ?Authenticatable $user = null;

    public function check()
    {
        return $this->user !== null;
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user ? $this->user->getAuthIdentifier() : null;
    }

    public function validate(array $credentials = [])
    {
        return true;
    }

    public function setUser(?Authenticatable $user)
    {
        $this->user = $user;
    }
}
