<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Library\Models\Track;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Http\Responses\DraftLyricsResponse;

class DraftLyricsTest extends HttpTestCase
{
    private const ROUTE_GET_DRAFT_LYRICS_FOR_TRACK = 'v1/draft/lyrics?track_id=%s';
    private const ROUTE_CREATE_DRAFT_LYRICS = 'v1/draft/lyrics';
    private const ROUTE_SHOW_DRAFT_LYRICS = 'v1/draft/lyrics/%s';
    private const ROUTE_EDIT_DRAFT_LYRICS = 'v1/draft/lyrics/%s';
    private const ROUTE_DELETE_DRAFT_LYRICS = 'v1/draft/lyrics/%s';

    private Track $track;

    protected function setUp(): void
    {
        parent::setUp();

        $this->track = $this->getTrackFactory()->create();
    }

    /**
     * @test
     */
    public function it_can_get_draft_lyric_for_a_track()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $response = $this->url(self::ROUTE_GET_DRAFT_LYRICS_FOR_TRACK, $draftLyrics->track_id)
            ->get();
        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($draftLyrics);

        $response = $this->asModerator()
            ->url(self::ROUTE_GET_DRAFT_LYRICS_FOR_TRACK, $draftLyrics->track_id)
            ->get();
        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($draftLyrics);

        $response = $this->asContributor()
            ->url(self::ROUTE_GET_DRAFT_LYRICS_FOR_TRACK, $draftLyrics->track_id)
            ->get();
        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($draftLyrics);
    }

    /**
     * @test
     */
    public function there_can_only_be_one_draft_lyrics_per_track()
    {
        $this->getDraftLyricsFactory()->create($this->track);

        $this->asContributor()
            ->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([
                'track_id' => $this->track->id
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['track_id' => 'taken']);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function moderator_can_create_new_draft()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument();

        $response = $this->asModerator()
            ->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([
                'track_id' => $this->track->id,
                'document' => $document->toArray()
            ]);
        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertTrackId($this->track->id)
            ->assertDocument($document);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function contributor_can_create_new_draft()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument();

        $response = $this->asContributor()
            ->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([
                'track_id' => $this->track->id,
                'document' => $document->toArray()
            ]);

        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertTrackId($this->track->id)
            ->assertDocument($document);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function unauthenticated_user_cannot_create_draft_lyrics()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument();

        $this->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([
                'track_id' => $this->track->id,
                'document' => $document->toArray()
            ])
            ->assertUnauthorized();
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function it_cannot_create_draft_lyrics_for_track_that_does_not_exist()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument();

        $trackIdDoesNotExist = Uuid::uuid1()->toString();
        $this->asContributor()
            ->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([
                'track_id' => $trackIdDoesNotExist,
                'document' => $document->toArray()
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['track_id' => 'does not exist']);
    }

    /**
     * @test
     */
    public function it_can_show_draft_lyrics_details()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $response = $this->asContributor()
            ->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyrics->id)
            ->get();
        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($draftLyrics);

        $response = $this->asModerator()
            ->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyrics->id)
            ->get();
        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($draftLyrics);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function unauthenticated_user_cannot_show_draft_lyrics_details()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyrics->id)
            ->get()
            ->assertUnauthorized();
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function it_can_edit_draft_lyrics_as_moderator()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument();

        $response = $this->asModerator()
            ->url(self::ROUTE_EDIT_DRAFT_LYRICS, $draftLyrics->id)
            ->patch([
                'document' => $updatedDocument->toArray()
            ]);

        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertDocument($updatedDocument);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function it_can_edit_draft_lyrics_as_contributor()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument();

        $response = $this->asContributor()
            ->url(self::ROUTE_EDIT_DRAFT_LYRICS, $draftLyrics->id)
            ->patch([
                'document' => $updatedDocument->toArray()
            ]);

        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertDocument($updatedDocument);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function unauthenticated_user_cannot_edit_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument();

        $this->url(self::ROUTE_EDIT_DRAFT_LYRICS, $draftLyrics->id)
            ->patch([
                'document' => $updatedDocument->toArray()
            ])
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_can_delete_draft_lyrics_as_moderator()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $response = $this->asModerator()
            ->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete();

        $response->assertSuccessful();
        $this->assertDatabaseMissing(DraftLyrics::class, ['id' => $draftLyrics->id]);
    }

    /**
     * @test
     */
    public function it_can_delete_draft_lyrics_as_contributor()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $response = $this->asContributor()
            ->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete();

        $response->assertSuccessful();
        $this->assertDatabaseMissing(DraftLyrics::class, ['id' => $draftLyrics->id]);
    }

    /**
     * @test
     */
    public function unauthenticated_user_cannot_delete_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete()
            ->assertUnauthorized();
    }
}