<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\ShouldBeStored;

class SocialAccountProviderIdChanged implements ShouldBeStored
{
    public string $id;
    public string $providerId;

    public function __construct(string $id, string $providerId)
    {
        $this->id = $id;
        $this->providerId = $providerId;
    }
}
