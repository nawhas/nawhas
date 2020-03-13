<?php

declare(strict_types=1);

namespace App\Database\Doctrine\Mappings;

use App\Entities\User;
use App\Enum\Role;
use LaravelDoctrine\Fluent\{EntityMapping, Fluent};

class UserMapping extends EntityMapping
{
    public function mapFor()
    {
        return User::class;
    }

    public function map(Fluent $map)
    {
        $map->uuidPrimaryKey();
        $map->string('name');
        $map->field(Role::class, 'role');
        $map->string('nickname')->unique()->nullable();
        $map->string('email')->unique();
        $map->string('password');
        $map->string('rememberToken')->nullable();
        $map->timestamps();
    }
}
