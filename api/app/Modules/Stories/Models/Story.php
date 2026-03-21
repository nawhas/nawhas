<?php

declare(strict_types=1);

namespace App\Modules\Stories\Models;

use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use App\Modules\Stories\Events\Stories\StoryBodyChanged;
use App\Modules\Stories\Events\Stories\StoryCreated;
use App\Modules\Stories\Events\Stories\StoryDeleted;
use App\Modules\Stories\Events\Stories\StoryDisplayDateChanged;
use App\Modules\Stories\Events\Stories\StoryExcerptChanged;
use App\Modules\Stories\Events\Stories\StoryHeroImageChanged;
use App\Modules\Stories\Events\Stories\StoryPublished;
use App\Modules\Stories\Events\Stories\StorySlugChanged;
use App\Modules\Stories\Events\Stories\StoryTitleChanged;
use App\Modules\Stories\Events\Stories\StoryUnpublished;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class Story extends Model implements TimestampedEntity
{
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;

    protected $guarded = [];

    protected $casts = [
        'display_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    /**
     * @param array{
     *     title: string,
     *     slug?: string|null,
     *     excerpt?: string|null,
     *     body?: string|null,
     *     hero_image_url?: string|null,
     *     display_date?: string|null,
     *     published?: bool,
     * } $data
     */
    public static function create(array $data): self
    {
        $id = Uuid::uuid1()->toString();
        $slug = isset($data['slug']) && $data['slug'] !== ''
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);

        $publishedAt = null;
        if (! empty($data['published'])) {
            $publishedAt = now()->toIso8601String();
        }

        event(new StoryCreated($id, [
            'slug' => $slug,
            'title' => $data['title'],
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'] ?? null,
            'hero_image_url' => $data['hero_image_url'] ?? null,
            'display_date' => $data['display_date'] ?? null,
            'published_at' => $publishedAt,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);

        return $model;
    }

    public function changeTitle(string $title): void
    {
        if ($title !== $this->title) {
            event(new StoryTitleChanged($this->id, $title));
        }
    }

    public function changeSlug(string $slug): void
    {
        $normalized = Str::slug($slug);
        if ($normalized !== $this->slug) {
            event(new StorySlugChanged($this->id, $normalized));
        }
    }

    public function changeExcerpt(?string $excerpt): void
    {
        if ($excerpt !== $this->excerpt) {
            event(new StoryExcerptChanged($this->id, $excerpt));
        }
    }

    public function changeBody(?string $body): void
    {
        if ($body !== $this->body) {
            event(new StoryBodyChanged($this->id, $body));
        }
    }

    public function changeHeroImageUrl(?string $heroImageUrl): void
    {
        if ($heroImageUrl !== $this->hero_image_url) {
            event(new StoryHeroImageChanged($this->id, $heroImageUrl));
        }
    }

    public function changeDisplayDate(?string $displayDate): void
    {
        $current = $this->display_date?->format('Y-m-d');
        if ($displayDate !== $current) {
            event(new StoryDisplayDateChanged($this->id, $displayDate));
        }
    }

    public function publish(): void
    {
        if (! $this->isPublished()) {
            event(new StoryPublished($this->id, now()->toIso8601String()));
        }
    }

    public function unpublish(): void
    {
        if ($this->isPublished()) {
            event(new StoryUnpublished($this->id));
        }
    }

    public function deleteStory(): void
    {
        event(new StoryDeleted($this->id));
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (($field === null || $field === 'id') && Uuid::isValid((string) $value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where($field ?? 'slug', $value)->firstOrFail();
    }
}
