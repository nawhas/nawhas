<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\User;

class UserFactory extends Factory
{
    protected function defaults(): array
    {
        return [
            'name' => fn () => $this->faker->name,
            'email' => fn () => $this->faker->email,
            'password' => fn () => $this->faker->password,
            'nickname' => fn () => $this->faker->userName,
        ];
    }

    public function create(Role $role, array $attributes = []): User
    {
        $values = $this->merge($attributes);

        return User::create(
            $role,
            $values->get('name'),
            $values->get('email'),
            $values->get('password'),
            null,
            $values->get('nickname')
        );
    }

    public function moderator(array $attributes = []): User
    {
        return $this->create(Role::MODERATOR(), $attributes);
    }

    public function contributor(array $attributes = []): User
    {
        return $this->create(Role::CONTRIBUTOR(), $attributes);
    }
}
