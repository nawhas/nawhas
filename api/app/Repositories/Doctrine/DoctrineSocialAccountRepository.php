<?php

declare(strict_types=1);

namespace App\Repositories\Doctrine;

use App\Entities\SocialAccount;
use App\Queries\SocialAccountQuery;
use App\Repositories\SocialAccountRepository;

class DoctrineSocialAccountRepository extends DoctrineRepository implements SocialAccountRepository
{
    public function findByProviderId(string $provider, string $providerId): ?SocialAccount
    {
        return $this->query()
            ->whereProvider($provider)
            ->whereProviderId($providerId)
            ->first();
    }

    public function query(): SocialAccountQuery
    {
        return SocialAccountQuery::make();
    }

    protected function entity(): string
    {
        return SocialAccount::class;
    }

    public function persist(SocialAccount ...$SocialAccount): void
    {
        $this->em->persist(...$SocialAccount);
    }

    public function remove(SocialAccount $SocialAccount): void
    {
        $this->em->remove($SocialAccount);
    }
}
