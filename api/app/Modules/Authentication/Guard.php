<?php

declare(strict_types=1);

namespace App\Modules\Authentication;

use App\Modules\Authentication\Models\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;

/**
 * This class wraps Laravel's Guard implementation and provides
 * a more concrete, type-safe interface specific to our domain.
 * Feel free to add more methods here as needed.
 */
class Guard
{
    private StatefulGuard $guard;

    public function __construct(AuthFactory $auth)
    {
        $this->guard = $auth->guard();
    }

    public function user(): ?User
    {
        return $this->guard->user();
    }

    public function check(): bool
    {
        return $this->guard->check();
    }

    public function id(): ?string
    {
        $id = $this->guard->id();

        return $id ? (string)$id : null;
    }
}
