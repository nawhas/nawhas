<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SocialAccountRegistered extends ShouldBeStored
{
    public string $id;
    public array $attributes = [];

    public function __construct(string $id, array $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }
}
