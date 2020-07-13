<?php

declare(strict_types=1);

namespace App\Modules\Authentication;

use App\Entities\User;
use Illuminate\Support\Facades\Auth;

/**
 * This class wraps Laravel's Guard implementation and provides
 * a more concrete, type-safe interface specific to our domain.
 * Feel free to add more methods here as needed.
 */
class Guard
{
    public function user(): ?User
    {
        return Auth::user();
    }

    public function check(): bool
    {
        return Auth::check();
    }

    public function id(): ?string
    {
        $id = Auth::id();

        return $id ? (string)$id : null;
    }

    public function login(User $user): void
    {
        Auth::login($user);
    }
}

