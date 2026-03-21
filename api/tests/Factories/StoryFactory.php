<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Modules\Stories\Models\Story;

class StoryFactory extends Factory
{
    protected function defaults(): array
    {
        return [
            'title' => fn () => $this->faker->sentence(4),
            'slug' => null,
            'excerpt' => null,
            'body' => null,
            'hero_image_url' => null,
            'display_date' => null,
            'published' => false,
        ];
    }

    /**
     * @param array{
     *     title?: string,
     *     slug?: string|null,
     *     excerpt?: string|null,
     *     body?: string|null,
     *     hero_image_url?: string|null,
     *     display_date?: string|null,
     *     published?: bool,
     * } $attributes
     */
    public function create(array $attributes = []): Story
    {
        $values = $this->merge($attributes);

        return Story::create([
            'title' => $values->get('title'),
            'slug' => $values->get('slug'),
            'excerpt' => $values->get('excerpt'),
            'body' => $values->get('body'),
            'hero_image_url' => $values->get('hero_image_url'),
            'display_date' => $values->get('display_date'),
            'published' => (bool) $values->get('published'),
        ]);
    }
}
