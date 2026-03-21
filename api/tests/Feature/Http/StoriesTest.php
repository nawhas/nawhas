<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Stories\Models\Story;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $this->getStoryFactory()->create([
            'title' => 'Draft only',
            'slug' => 'draft-only',
            'body' => 'Hidden',
            'published' => false,
        ]);
        $this->getStoryFactory()->create([
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
    public function contributors_only_see_published_stories_in_index(): void
    {
        $this->getStoryFactory()->create([
            'title' => 'Draft',
            'slug' => 'contrib-draft',
            'published' => false,
        ]);
        $this->getStoryFactory()->create([
            'title' => 'Live',
            'slug' => 'contrib-live',
            'published' => true,
        ]);

        $response = $this->asContributor()
            ->url(self::ROUTE_INDEX)
            ->get();

        PaginatedCollectionResponse::from($response)
            ->assertSuccessful()
            ->assertTotal(1);
        $response->assertJsonPath('data.0.slug', 'contrib-live');
    }

    /**
     * @test
     */
    public function guests_get_404_for_unpublished_story_by_slug(): void
    {
        $story = $this->getStoryFactory()->create([
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
        $story = $this->getStoryFactory()->create([
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
    public function moderator_can_fetch_unpublished_story_by_uuid(): void
    {
        $story = $this->getStoryFactory()->create([
            'title' => 'UUID draft',
            'slug' => 'uuid-draft',
            'published' => false,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_SHOW, $story->id)
            ->get()
            ->assertSuccessful()
            ->assertJsonPath('slug', 'uuid-draft');
    }

    /**
     * @test
     */
    public function moderators_see_drafts_in_index(): void
    {
        $this->getStoryFactory()->create([
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
        $this->getStoryFactory()->create([
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
        $this->getStoryFactory()->create([
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
        $this->getStoryFactory()->create([
            'title' => 'First',
            'slug' => 'first-slug',
            'published' => false,
        ]);
        $second = $this->getStoryFactory()->create([
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
        $this->getStoryFactory()->create([
            'title' => 'Draft',
            'slug' => 'draft-slug',
            'published' => false,
        ]);
        $this->getStoryFactory()->create([
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

    /**
     * @test
     */
    public function moderator_can_upload_story_hero_image(): void
    {
        Storage::fake();

        $story = $this->getStoryFactory()->create([
            'title' => 'Hero upload',
            'slug' => 'hero-upload-slug',
            'published' => false,
        ]);

        $file = UploadedFile::fake()->create('hero.jpg', 50, 'image/jpeg');

        $response = $this->asModerator()->post(
            sprintf('v1/stories/%s/hero', $story->id),
            ['hero_image' => $file],
            ['Accept' => 'application/json']
        );

        $response->assertSuccessful();

        $path = $story->fresh()->hero_image_url;
        $this->assertNotNull($path);
        $this->assertStringStartsWith('stories/hero-upload-slug/', $path);
        Storage::assertExists($path);
        $this->assertIsString($response->json('heroImageUrl'));
    }

    /**
     * @test
     */
    public function guests_cannot_upload_story_hero_image(): void
    {
        Storage::fake();

        $story = $this->getStoryFactory()->create([
            'title' => 'Nope',
            'slug' => 'nope-hero',
            'published' => true,
        ]);

        $file = UploadedFile::fake()->create('x.jpg', 50, 'image/jpeg');

        $this->post(
            sprintf('v1/stories/%s/hero', $story->id),
            ['hero_image' => $file],
            ['Accept' => 'application/json']
        )->assertUnauthorized();
    }

    /**
     * @test
     */
    public function contributors_cannot_upload_story_hero_image(): void
    {
        Storage::fake();

        $story = $this->getStoryFactory()->create([
            'title' => 'Contrib',
            'slug' => 'contrib-hero',
            'published' => true,
        ]);

        $file = UploadedFile::fake()->create('x.jpg', 50, 'image/jpeg');

        $this->asContributor()
            ->post(
                sprintf('v1/stories/%s/hero', $story->id),
                ['hero_image' => $file],
                ['Accept' => 'application/json']
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function moderator_can_create_story_with_display_date(): void
    {
        $this->asModerator()
            ->url(self::ROUTE_INDEX)
            ->post([
                'title' => 'Dated story',
                'slug' => 'dated-story',
                'display_date' => '2022-11-03',
                'published' => false,
            ])
            ->assertSuccessful()
            ->assertJsonPath('displayDate', '2022-11-03');

        /** @var Story $row */
        $row = Story::query()->where('slug', 'dated-story')->firstOrFail();
        $this->assertSame('2022-11-03', $row->display_date->format('Y-m-d'));
    }

    /**
     * @test
     */
    public function moderator_cannot_patch_story_with_invalid_display_date(): void
    {
        $story = $this->getStoryFactory()->create([
            'title' => 'Patch date',
            'slug' => 'patch-date',
            'published' => false,
        ]);

        $this->asModerator()
            ->url(self::ROUTE_SHOW, $story->id)
            ->patch(['display_date' => 'not-a-date'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['display_date']);
    }

    /**
     * @test
     */
    public function moderator_hero_upload_without_file_is_unprocessable(): void
    {
        $story = $this->getStoryFactory()->create([
            'title' => 'No file',
            'slug' => 'no-file-hero',
            'published' => false,
        ]);

        $this->asModerator()
            ->post(
                sprintf('v1/stories/%s/hero', $story->id),
                [],
                ['Accept' => 'application/json']
            )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['hero_image']);
    }

    /**
     * @test
     */
    public function guests_see_external_hero_url_unchanged_on_published_story(): void
    {
        $url = 'https://cdn.example.test/stories/hero.jpg';
        $this->getStoryFactory()->create([
            'title' => 'Hero ext',
            'slug' => 'hero-ext',
            'body' => 'B',
            'hero_image_url' => $url,
            'published' => true,
        ]);

        $this->url(self::ROUTE_SHOW, 'hero-ext')
            ->get()
            ->assertSuccessful()
            ->assertJsonPath('heroImageUrl', $url);
    }
}
