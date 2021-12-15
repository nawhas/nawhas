<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Events;

use App\Modules\Authentication\Enum\Role;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserRoleChanged extends ShouldBeStored
{
    public function __construct(
        public string $id,
        public Role $role
    ) {}
}
