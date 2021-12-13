<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Modules\Library\Models\Reciter;

class ReciterFactory extends Factory
{
    protected function defaults(): array
    {
        return [
            'name' => fn () => $this->faker->name,
            'description' => fn () => $this->faker->email,
        ];
    }

    public function create(array $attributes = []): Reciter
    {
        $values = $this->merge($attributes);

        return Reciter::create(
            $values->get('name'),
            $values->get('description'),
        );
    }
}
