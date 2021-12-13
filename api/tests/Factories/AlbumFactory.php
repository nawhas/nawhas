<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;

class AlbumFactory extends Factory
{
    protected function defaults(): array
    {
        return [
            'title' => fn () => $this->faker->sentence,
            'year' => fn () => (string) $this->faker->numberBetween(1970, 2050),
            'artwork' => null,
        ];
    }

    public function create(?Reciter $reciter = null, array $attributes = []): Album
    {
        $reciter = $reciter ?? $this->getReciterFactory()->create();
        $values = $this->merge($attributes);

        return Album::create(
            $reciter,
            $values->get('title'),
            $values->get('year'),
            $values->get('artwork'),
        );
    }
}
