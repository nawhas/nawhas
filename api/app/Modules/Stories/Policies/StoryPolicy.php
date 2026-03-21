<?php

declare(strict_types=1);

namespace App\Modules\Stories\Policies;

use App\Modules\Authentication\Models\User;
use App\Modules\Stories\Models\Story;

class StoryPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user !== null && $user->isModerator();
    }

    public function view(?User $user, Story $story): bool
    {
        if ($story->isPublished()) {
            return true;
        }

        return $user !== null && $user->isModerator();
    }

    public function create(User $user): bool
    {
        return $user->isModerator();
    }

    public function update(User $user, Story $story): bool
    {
        return $user->isModerator();
    }

    public function delete(User $user, Story $story): bool
    {
        return $user->isModerator();
    }
}
