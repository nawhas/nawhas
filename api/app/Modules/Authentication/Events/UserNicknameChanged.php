<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserNicknameChanged extends ShouldBeStored
{
    public function __construct(
        public string $id,
        public string $nickname,
    ) {}
}
