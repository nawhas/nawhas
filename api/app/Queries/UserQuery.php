<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\User;

class UserQuery extends Query
{
    public function whereEmail(string $email): self
    {
        $this->builder->andWhere('t.email = :email')
            ->setParameter('email', $email);

        return $this;
    }

    protected static function entity(): string
    {
        return User::class;
    }
}