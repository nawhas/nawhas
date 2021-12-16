<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use App\Exceptions\NotImplementedException;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

abstract class SocialAccountRegistered extends ShouldBeStored
{
    public function __construct(
        public string $id,
        public array $attributes = []
    ) {
        throw new NotImplementedException();
    }
}
