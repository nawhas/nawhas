<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Stories\Models\Story;

use function App\Support\uuid;

class StoryEventsTest extends EventsTestCase
{
    /**
     * @test
     */
    #[CoversEvent('story.created')]
    public function it_can_replay_story_created_event(): void
    {
        $id = uuid();
        $slug = 'sample-story';
        $title = 'Sample Story';

        $this->event('story.created', [
            'id' => $id,
            'attributes' => [
                'slug' => $slug,
                'title' => $title,
                'excerpt' => 'Excerpt',
                'body' => 'Body',
                'hero_image_url' => 'https://example.test/hero.jpg',
                'display_date' => '2024-06-01',
                'published_at' => null,
            ],
        ]);

        $this->replay();

        $story = Story::find($id);
        $this->assertNotNull($story);
        $this->assertSame($slug, $story->slug);
        $this->assertSame($title, $story->title);
        $this->assertSame('Excerpt', $story->excerpt);
        $this->assertSame('Body', $story->body);
        $this->assertSame('https://example.test/hero.jpg', $story->hero_image_url);
        $this->assertTrue($story->display_date->isSameDay('2024-06-01'));
        $this->assertNull($story->published_at);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.changed.title')]
    public function it_can_replay_story_title_changed_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.changed.title', [
            'id' => $story->id,
            'title' => 'New Title',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertSame('New Title', $story->title);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.changed.slug')]
    public function it_can_replay_story_slug_changed_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.changed.slug', [
            'id' => $story->id,
            'slug' => 'new-slug',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertSame('new-slug', $story->slug);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.changed.excerpt')]
    public function it_can_replay_story_excerpt_changed_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.changed.excerpt', [
            'id' => $story->id,
            'excerpt' => 'Updated excerpt',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertSame('Updated excerpt', $story->excerpt);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.changed.body')]
    public function it_can_replay_story_body_changed_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.changed.body', [
            'id' => $story->id,
            'body' => 'Updated body',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertSame('Updated body', $story->body);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.changed.hero_image')]
    public function it_can_replay_story_hero_image_changed_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.changed.hero_image', [
            'id' => $story->id,
            'heroImageUrl' => 'https://example.test/new.jpg',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertSame('https://example.test/new.jpg', $story->hero_image_url);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.changed.display_date')]
    public function it_can_replay_story_display_date_changed_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.changed.display_date', [
            'id' => $story->id,
            'displayDate' => '2025-01-15',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertNotNull($story->display_date);
        $this->assertTrue($story->display_date->isSameDay('2025-01-15'));
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.published')]
    public function it_can_replay_story_published_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.published', [
            'id' => $story->id,
            'publishedAt' => '2024-07-01T12:00:00+00:00',
        ]);

        $this->replay();

        $story->refresh();
        $this->assertNotNull($story->published_at);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.published')]
    #[CoversEvent('story.unpublished')]
    public function it_can_replay_story_unpublished_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.published', [
            'id' => $story->id,
            'publishedAt' => '2024-07-01T12:00:00+00:00',
        ]);
        $this->replay();

        $story->refresh();
        $this->assertNotNull($story->published_at);

        $this->event('story.unpublished', [
            'id' => $story->id,
        ]);
        $this->replay();

        $story->refresh();
        $this->assertNull($story->published_at);
    }

    /**
     * @test
     */
    #[CoversEvent('story.created')]
    #[CoversEvent('story.deleted')]
    public function it_can_replay_story_deleted_event(): void
    {
        $id = uuid();
        $this->event('story.created', $this->newStoryPayload($id));
        $this->replay();
        $story = Story::find($id);
        $this->assertNotNull($story);

        $this->event('story.deleted', [
            'id' => $story->id,
        ]);

        $this->replay();

        $this->assertNull(Story::find($story->id));
    }

    /**
     * @return array{id: string, attributes: array<string, mixed>}
     */
    private function newStoryPayload(string $id): array
    {
        return [
            'id' => $id,
            'attributes' => [
                'slug' => 'story-'.$id,
                'title' => 'Story '.$id,
                'excerpt' => null,
                'body' => null,
                'hero_image_url' => null,
                'display_date' => null,
                'published_at' => null,
            ],
        ];
    }
}
