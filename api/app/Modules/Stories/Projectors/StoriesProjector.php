<?php

declare(strict_types=1);

namespace App\Modules\Stories\Projectors;

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
use App\Modules\Stories\Models\Story;
use Carbon\Carbon;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class StoriesProjector extends Projector
{
    public function onStoryCreated(StoryCreated $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $publishedAt = $data->get('published_at');
        if ($publishedAt !== null) {
            $data->put('published_at', Carbon::parse((string) $publishedAt));
        }

        $displayDate = $data->get('display_date');
        if ($displayDate !== null && $displayDate !== '') {
            $data->put('display_date', Carbon::parse((string) $displayDate)->startOfDay());
        }

        $story = new Story($data->all());

        $story->saveOrFail();
    }

    public function onStoryTitleChanged(StoryTitleChanged $event): void
    {
        $story = Story::retrieve($event->id);
        $story->title = $event->title;
        $story->saveOrFail();
    }

    public function onStorySlugChanged(StorySlugChanged $event): void
    {
        $story = Story::retrieve($event->id);
        $story->slug = $event->slug;
        $story->saveOrFail();
    }

    public function onStoryExcerptChanged(StoryExcerptChanged $event): void
    {
        $story = Story::retrieve($event->id);
        $story->excerpt = $event->excerpt;
        $story->saveOrFail();
    }

    public function onStoryBodyChanged(StoryBodyChanged $event): void
    {
        $story = Story::retrieve($event->id);
        $story->body = $event->body;
        $story->saveOrFail();
    }

    public function onStoryHeroImageChanged(StoryHeroImageChanged $event): void
    {
        $story = Story::retrieve($event->id);
        $story->hero_image_url = $event->heroImageUrl;
        $story->saveOrFail();
    }

    public function onStoryDisplayDateChanged(StoryDisplayDateChanged $event): void
    {
        $story = Story::retrieve($event->id);
        $story->display_date = $event->displayDate !== null
            ? Carbon::parse($event->displayDate)->startOfDay()
            : null;
        $story->saveOrFail();
    }

    public function onStoryPublished(StoryPublished $event): void
    {
        $story = Story::retrieve($event->id);
        $story->published_at = Carbon::parse($event->publishedAt);
        $story->saveOrFail();
    }

    public function onStoryUnpublished(StoryUnpublished $event): void
    {
        $story = Story::retrieve($event->id);
        $story->published_at = null;
        $story->saveOrFail();
    }

    public function onStoryDeleted(StoryDeleted $event): void
    {
        $story = Story::retrieve($event->id);
        $story->delete();
    }

    public function resetState(): void
    {
        Story::truncate();
    }
}
