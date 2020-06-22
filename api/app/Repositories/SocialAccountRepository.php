<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\SocialAccount;
use App\Queries\SocialAccountQuery;

interface SocialAccountRepository
{
    public function findByProviderId(string $provider, string $providerId): ?SocialAccount;
    public function query(): SocialAccountQuery;
    public function persist(SocialAccount ...$userProvider): void;
    public function remove(SocialAccount $userProvider): void;
}
