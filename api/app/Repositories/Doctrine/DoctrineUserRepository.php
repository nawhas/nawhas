<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\User;
use App\Queries\UserQuery;
use App\Repositories\UserRepository;

class DoctrineUserRepository extends DoctrineRepository implements UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return $this->query()
            ->whereEmail($email)
            ->first();
    }

    public function query(): UserQuery
    {
        return UserQuery::make();
    }

    protected function entity(): string
    {
        return User::class;
    }

    public function persist(User ...$user): void
    {
        $this->em->persist(...$user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}
