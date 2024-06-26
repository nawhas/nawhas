<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Library\Models\DraftLyrics;
use App\Modules\Library\Models\Track;
use App\Modules\Lyrics\Documents\Format;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Http\Responses\DraftLyricsResponse;

class DraftLyricsTest extends HttpTestCase
{
    private const ROUTE_GET_DRAFT_LYRICS_FOR_TRACK = 'v1/drafts/lyrics?track_id=%s';
    private const ROUTE_CREATE_DRAFT_LYRICS = 'v1/drafts/lyrics';
    private const ROUTE_LOCK_DRAFT_LYRICS = 'v1/drafts/lyrics/%s/lock';
    private const ROUTE_UNLOCK_DRAFT_LYRICS = 'v1/drafts/lyrics/%s/unlock';
    private const ROUTE_SHOW_DRAFT_LYRICS = 'v1/drafts/lyrics/%s';
    private const ROUTE_EDIT_DRAFT_LYRICS = 'v1/drafts/lyrics/%s';
    private const ROUTE_DELETE_DRAFT_LYRICS = 'v1/drafts/lyrics/%s';
    private const ROUTE_PUBLISH_DRAFT_LYRICS = 'v1/drafts/lyrics/%s/publish';

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
     */
    public function track_id_is_required()
    {
        $this->asContributor()
            ->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['track_id' => 'required']);

        $this->asModerator()
            ->url(self::ROUTE_CREATE_DRAFT_LYRICS)
            ->post([])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['track_id' => 'required']);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function moderator_can_create_new_draft_with_plain_text_format()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);

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
    public function moderator_can_create_new_draft_with_json_format()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);

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
    public function contributor_can_create_new_draft_with_plain_text_format()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);

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
    public function contributor_can_create_new_draft_with_json_format()
    {
        $document = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);

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
    public function it_can_show_draft_lyrics_details_for_plain_text_format()
    {
        $draftLyricsPlainText = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)]);

        $responsePlainText = $this->asContributor()
            ->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyricsPlainText->id)
            ->get();
        DraftLyricsResponse::from($responsePlainText)
            ->assertSuccessful()
            ->assertMatches($draftLyricsPlainText);

        $responsePlainTextModerator = $this->asModerator()
            ->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyricsPlainText->id)
            ->get();
        DraftLyricsResponse::from($responsePlainTextModerator)
            ->assertSuccessful()
            ->assertMatches($draftLyricsPlainText);
    }

    /**
     * @test
     */
    public function it_can_show_draft_lyrics_details_for_json_format()
    {
        $draftLyricsJson = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1)]);

        $responseJson = $this->asContributor()
            ->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyricsJson->id)
            ->get();
        DraftLyricsResponse::from($responseJson)
            ->assertSuccessful()
            ->assertMatches($draftLyricsJson);

        $responseJsonModerator = $this->asModerator()
            ->url(self::ROUTE_SHOW_DRAFT_LYRICS, $draftLyricsJson->id)
            ->get();
        DraftLyricsResponse::from($responseJsonModerator)
            ->assertSuccessful()
            ->assertMatches($draftLyricsJson);
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
    public function it_can_edit_draft_lyrics_from_plain_text_to_plain_text_as_moderator()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);

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
    public function it_can_edit_draft_lyrics_from_plain_text_to_json_as_moderator()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);

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
    public function it_can_edit_draft_lyrics_from_json_to_plain_text_as_moderator()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);

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
    public function it_can_edit_draft_lyrics_from_json_to_json_as_moderator()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);

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
    public function it_can_edit_draft_lyrics_from_plain_text_to_plain_text_as_contributor()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);

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
    public function it_can_edit_draft_lyrics_from_plain_text_to_json_as_contributor()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);

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
    public function it_can_edit_draft_lyrics_from_json_to_plain_text_as_contributor()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);

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
    public function it_can_edit_draft_lyrics_from_json_to_json_as_contributor()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1)]);
        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);

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
    public function contributor_cannot_delete_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $response = $this->asContributor()
            ->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete();

        $response->assertForbidden();
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

    /**
     * @test
     * @throws \JsonException
     */
    public function it_allows_user_to_edit_a_track_which_they_have_locked()
    {
        $requestAsContributor = $this->asContributor();

        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $requestAsContributor
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $updatedDocument = $this->getDraftLyricsFactory()->generateDocument();
        $response = $requestAsContributor
            ->url(self::ROUTE_EDIT_DRAFT_LYRICS, $draftLyrics->id)
            ->patch([
                'document' => $updatedDocument->toArray()
            ]);

        DraftLyricsResponse::from($response)
            ->assertSuccessful()
            ->assertTrackId($this->track->id)
            ->assertDocument($updatedDocument);
    }

    /**
     * @test
     */
    public function it_prevents_second_user_from_locking_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->asModerator()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response = $this->asContributor()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertNoContent(423);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function it_prevents_updating_draft_lyrics_for_locked_track_if_not_same_user()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

         $this->asModerator()
             ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
             ->post();

        $response = $this->asContributor()
            ->url(self::ROUTE_EDIT_DRAFT_LYRICS, $draftLyrics->id)
            ->patch([
                'document' => $this->getDraftLyricsFactory()->generateDocument()->toArray()
            ]);

        $response->assertStatus(423)
            ->assertSeeText('Draft Lyrics is currently locked by another user.');
    }

    /**
     * @test
     */
    public function it_allows_moderator_to_delete_draft_lyrics_if_contributor_has_locked_it()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->asContributor()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response = $this->asModerator()
            ->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete();

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_moderator_to_delete_draft_lyrics_if_same_moderator_has_locked_it()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->asModerator()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response = $this->asModerator()
            ->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete();

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_second_moderator_to_delete_draft_lyrics_if_first_moderator_has_locked_it()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->asModerator()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $newModerator = $this->getUserFactory()->create(Role::Moderator);
        $response = $this->actingAs($newModerator)
            ->url(self::ROUTE_DELETE_DRAFT_LYRICS, $draftLyrics->id)
            ->delete();

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_second_user_to_lock_after_first_user_saves()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);
        $requestAsModerator = $this->asModerator();

        $requestAsModerator
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post()
            ->assertSuccessful();

        $requestAsModerator
            ->url(self::ROUTE_EDIT_DRAFT_LYRICS, $draftLyrics->id)
            ->patch([
                'document' => $this->getDraftLyricsFactory()->generateDocument()->toArray()
            ])
            ->assertSuccessful();

        $this->asContributor()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post()
            ->assertSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_same_moderator_to_unlock_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $this->asModerator()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post()
            ->assertSuccessful();

        $response = $this->asModerator()
            ->url(self::ROUTE_UNLOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_different_moderator_to_unlock_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);
        $moderator1 = $this->asModerator();
        $moderator2 = $this->asModerator();

        $moderator1
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post()
            ->assertSuccessful();

        $response = $moderator2
            ->url(self::ROUTE_UNLOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function it_prevents_contributor_from_unlocking_draft_lyrics_if_moderator_has_locked()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);


        $this->asModerator()
            ->url(self::ROUTE_LOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post()
            ->assertSuccessful();


        $response = $this->asContributor()
            ->url(self::ROUTE_UNLOCK_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertNoContent(423);
    }

    /**
     * @test
     */
    public function contributor_cannot_publish_draft_lyrics()
    {
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track);

        $response = $this->asContributor()
            ->url(self::ROUTE_PUBLISH_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertForbidden();
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function moderator_can_publish_draft_lyrics_plain_text()
    {
        $originalDocument = $this->getDraftLyricsFactory()->generateDocument(Format::PlainText);
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $originalDocument]);

        $response = $this->asModerator()
            ->url(self::ROUTE_PUBLISH_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertSuccessful();

        $updatedTrack = $this->track->refresh();
        $this->assertEquals($originalDocument, $updatedTrack->lyrics);
    }

    /**
     * @test
     * @throws \JsonException
     */
    public function moderator_can_publish_draft_lyrics_json_v2()
    {
        $originalDocument = $this->getDraftLyricsFactory()->generateDocument(Format::JsonV1);
        $draftLyrics = $this->getDraftLyricsFactory()->create($this->track, ['document' => $originalDocument]);

        $response = $this->asModerator()
            ->url(self::ROUTE_PUBLISH_DRAFT_LYRICS, $draftLyrics->id)
            ->post();

        $response->assertSuccessful();

        $updatedTrack = $this->track->refresh();
        $this->assertEquals($originalDocument, $updatedTrack->lyrics);
    }
}
