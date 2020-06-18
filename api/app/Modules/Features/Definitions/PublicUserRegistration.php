<?php

declare(strict_types=1);

namespace App\Modules\Features\Definitions;

use App\Entities\User;

class PublicUserRegistration implements Feature
{
    public const NAME = 'registration.public';

    public function enabled(?User $user): bool
    {
        return true;
    }

    public function name(): string
    {
        return self::NAME;
    }
}
