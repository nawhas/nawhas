<?php

namespace App\Modules\Library\Policies;

use App\Modules\Authentication\Models\User;
use App\Modules\Library\Models\Reciter;

class ReciterPolicy
{
    public function create(User $user): bool
    {
        return $user->isModerator();
    }

    public function update(User $user, Reciter $reciter): bool
    {
        return $user->isModerator();
    }

    public function delete(User $user, Reciter $reciter): bool
    {
        return $user->isModerator();
    }
}
