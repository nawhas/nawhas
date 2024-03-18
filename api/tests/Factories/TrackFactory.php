<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\User;
use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;

class TrackFactory extends Factory
{
    protected function defaults(): array
    {
        return [
            'title' => fn () => $this->faker->sentence,
        ];
    }

    public function create(?Album $album = null, array $attributes = []): Track
    {
        $album = $album ?? $this->getAlbumFactory()->create();
        $values = $this->merge($attributes);

        return Track::create(
            $album,
            $values->get('title'),
        );
    }
}
