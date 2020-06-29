<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\User;
use App\Queries\UserQuery;

interface UserRepository
{
    public function findByEmail(string $email): ?User;
    public function query(): UserQuery;
    public function persist(User ...$user): void;
    public function remove(User $user): void;
}
