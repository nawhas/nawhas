<?php

declare(strict_types=1);

namespace App\Modules\Features\Definitions;

use App\Modules\Authentication\Models\User;

class PublicUserRegistration implements Feature
{
    public const NAME = 'registration.public';

    public function enabled(?User $user): bool
    {
        return false;
    }

    public function name(): string
    {
        return self::NAME;
    }
}
