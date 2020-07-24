<?php


namespace App\Modules\Authentication\Events;


use App\Enum\Role;
use Spatie\EventSourcing\ShouldBeStored;

class UserRoleChanged implements ShouldBeStored
{
    public string $id;
    public Role $role;

    public function __construct(string $id, Role $role)
    {
        $this->id = $id;
        $this->role = $role;
    }
}
