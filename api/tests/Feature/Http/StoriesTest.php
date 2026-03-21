<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Stories\Models\Story;
use Tests\Feature\Http\Responses\PaginatedCollectionResponse;

class StoriesTest extends HttpTestCase
{
    private const ROUTE_INDEX = 'v1/stories';
    private const ROUTE_SHOW = 'v1/stories/%s';

    /**
     * @test
     */
    public function guests_only_see_published_stories_in_index(): void
    {
        Story::create([
            'title' => 'Draft only',
            'slug' => 'draft-only',
            'body' => 'Hidden',
            'published' => false,
        ]);
        Story::create([
            'title' => 'Public',
            'slug' => 'public-one',
            'body' => 'Hello',
            'published' => true,
        ]);

        $response = $this->url(self::ROUTE_INDEX)->get();

        PaginatedCollectionResponse::from($response)
            ->assertSuccessful()
            ->assertTotal(1);
        $response->assertJsonPath('data.0.slug', 'public-one');
    }

    /**
     * @test
     */
    public function guests_get_404_for_unpublished_story_by_slug(): void
    {
        $story = Story::create([
            'title' => 'Draft',
            'slug' => 'secret-draft',
            'published' => false,
        ]);

        $this->url(self::ROUTE_SHOW, $story->slug)
            ->get()
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function guests_can_fetch_published_story_by_slug(): void
    {
        $story = Story::create([
            'title' => 'Visible',
            'slug' => 'visible-slug',
            'body' => 'Body text',
            'published' => true,
        ]);

        $response = $this->url(self::ROUTE_SHOW, $story->slug)->get();

        $response->assertSuccessful()
            ->assertJsonPath('slug', 'visible-slug')
            ->assertJsonPath('title', 'Visible');
    }

    /**
     * @test
     */
    public function moderators_see_drafts_in_index(): void
    {
        Story::create([
            'title' => 'Draft',
            'slug' => 'mod-draft',
            'published' => false,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_INDEX)
            ->get()
            ->assertSuccessful()
            ->assertJsonPath('data.0.slug', 'mod-draft');
    }

    /**
     * @test
     */
    public function moderator_can_create_update_publish_and_delete(): void
    {
        $this->asModerator()
            ->url(self::ROUTE_INDEX)
            ->post([
                'title' => 'New story',
                'slug' => 'new-story',
                'body' => 'Content',
                'published' => false,
            ])
            ->assertSuccessful()
            ->assertJsonPath('slug', 'new-story');

        /** @var Story $story */
        $story = Story::query()->where('slug', 'new-story')->firstOrFail();

        $published = $this->asModerator()
            ->url(self::ROUTE_SHOW, $story->id)
            ->patch(['published' => true])
            ->assertSuccessful();

        $this->assertNotNull($published->json('publishedAt'));

        $this->url(self::ROUTE_SHOW, $story->slug)
            ->get()
            ->assertSuccessful();

        $this->asModerator()
            ->url(self::ROUTE_SHOW, $story->id)
            ->delete()
            ->assertNoContent();

        $this->assertDatabaseMissing('stories', ['id' => $story->id], 'data');
    }

    /**
     * @test
     */
    public function guests_cannot_mutate_stories(): void
    {
        $this->url(self::ROUTE_INDEX)
            ->post(['title' => 'X'])
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function moderator_cannot_create_story_with_duplicate_slug(): void
    {
        Story::create([
            'title' => 'Existing',
            'slug' => 'taken-slug',
            'published' => false,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_INDEX)
            ->post([
                'title' => 'Another',
                'slug' => 'taken-slug',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * @test
     */
    public function moderator_cannot_create_story_when_title_slugs_to_existing_slug(): void
    {
        Story::create([
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'published' => false,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_INDEX)
            ->post([
                'title' => 'Hello World',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * @test
     */
    public function moderator_cannot_update_story_to_another_stories_slug(): void
    {
        Story::create([
            'title' => 'First',
            'slug' => 'first-slug',
            'published' => false,
        ]);
        $second = Story::create([
            'title' => 'Second',
            'slug' => 'second-slug',
            'published' => false,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_SHOW, $second->id)
            ->patch(['slug' => 'first-slug'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    /**
     * @test
     */
    public function moderator_index_lists_published_stories_before_drafts(): void
    {
        Story::create([
            'title' => 'Draft',
            'slug' => 'draft-slug',
            'published' => false,
        ]);
        Story::create([
            'title' => 'Published',
            'slug' => 'pub-slug',
            'published' => true,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_INDEX)
            ->get()
            ->assertSuccessful()
            ->assertJsonPath('data.0.slug', 'pub-slug')
            ->assertJsonPath('data.1.slug', 'draft-slug');
    }
}
