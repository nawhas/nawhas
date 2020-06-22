<?php

declare(strict_types=1);

namespace App\Queries;

use App\Entities\SocialAccount;

class SocialAccountQuery extends Query
{
    public function whereProvider(string $provider): self
    {
        $this->builder->andWhere('t.provider = :provider')
            ->setParameter('provider', $provider);

        return $this;
    }

    public function whereProviderId(string $providerId): self
    {
        $this->builder->andWhere('t.providerId = :providerId')
            ->setParameter('providerId', $providerId);

        return $this;
    }

    protected static function entity(): string
    {
        return SocialAccount::class;
    }
}