<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use Ramsey\Uuid\Uuid;
use Tests\WithSearchIndex;

/**
 * Authorization and negative-path coverage for nested library routes.
 *
 * Mutations under albums/tracks use auth:sanctum without ReciterPolicy — contributors
 * and moderators are both allowed. Guests receive 401.
 *
 * | Action | Guest | Contributor | Moderator |
 * |--------|:-----:|:-----------:|:---------:|
 * | POST …/albums | 401 | 200 | 200 |
 * | PATCH …/albums/{year} | 401 | 200 | 200 |
 * | DELETE …/albums/{year} | 401 | 204 | 204 |
 * | POST …/tracks | 401 | 200 | 200 |
 * | PATCH …/tracks/{slug} | 401 | 200 | 200 |
 * | DELETE …/tracks/{slug} | 401 | 204 | 204 |
 */
class LibraryApiAuthzAndNegativePathsTest extends HttpTestCase
{
    use WithSearchIndex;

    private Reciter $reciter;

    private Album $album;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reciter = $this->getReciterFactory()->create();
        $this->album = $this->getAlbumFactory()->create($this->reciter, [
            'year' => (string) random_int(5000, 5999),
        ]);
    }

    /**
     * @test
     */
    public function guests_cannot_mutate_albums(): void
    {
        $year = (string) random_int(6000, 6999);

        $this->url('v1/reciters/%s/albums', $this->reciter->id)
            ->post(['title' => 'Guest blocked', 'year' => $year])
            ->assertUnauthorized();

        $this->url('v1/reciters/%s/albums/%s', $this->reciter->id, $this->album->year)
            ->patch(['title' => 'Nope'])
            ->assertUnauthorized();

        $this->url('v1/reciters/%s/albums/%s', $this->reciter->id, $this->album->year)
            ->delete()
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function contributors_and_moderators_can_mutate_albums(): void
    {
        $createYear = (string) random_int(7000, 7099);
        $this->asContributor()
            ->url('v1/reciters/%s/albums', $this->reciter->id)
            ->post(['title' => 'Contributor album', 'year' => $createYear])
            ->assertOk();

        $albumContributor = Album::query()
            ->where('reciter_id', $this->reciter->id)
            ->where('year', $createYear)
            ->firstOrFail();

        $this->asContributor()
            ->url('v1/reciters/%s/albums/%s', $this->reciter->id, $createYear)
            ->patch(['title' => 'Updated by contributor'])
            ->assertOk();

        $this->asContributor()
            ->url('v1/reciters/%s/albums/%s', $this->reciter->id, $createYear)
            ->delete()
            ->assertNoContent();

        $this->assertNull(Album::find($albumContributor->id));

        $createYearMod = (string) random_int(7100, 7199);
        $this->asModerator()
            ->url('v1/reciters/%s/albums', $this->reciter->id)
            ->post(['title' => 'Moderator album', 'year' => $createYearMod])
            ->assertOk();

        $this->asModerator()
            ->url('v1/reciters/%s/albums/%s', $this->reciter->id, $createYearMod)
            ->patch(['title' => 'Updated by moderator'])
            ->assertOk();

        $this->asModerator()
            ->url('v1/reciters/%s/albums/%s', $this->reciter->id, $createYearMod)
            ->delete()
            ->assertNoContent();
    }

    /**
     * @test
     */
    public function guests_cannot_mutate_tracks(): void
    {
        $track = $this->getTrackFactory()->create($this->album);

        $this->url(
            'v1/reciters/%s/albums/%s/tracks',
            $this->reciter->id,
            $this->album->year
        )
            ->post(['title' => 'Guest track'])
            ->assertUnauthorized();

        $this->url(
            'v1/reciters/%s/albums/%s/tracks/%s',
            $this->reciter->id,
            $this->album->year,
            $track->slug
        )
            ->patch(['title' => 'Nope'])
            ->assertUnauthorized();

        $this->url(
            'v1/reciters/%s/albums/%s/tracks/%s',
            $this->reciter->id,
            $this->album->year,
            $track->slug
        )
            ->delete()
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function contributors_and_moderators_can_mutate_tracks(): void
    {
        $this->asContributor()
            ->url(
                'v1/reciters/%s/albums/%s/tracks',
                $this->reciter->id,
                $this->album->year
            )
            ->post(['title' => 'Contributor track'])
            ->assertOk();

        $track = Track::query()
            ->where('album_id', $this->album->id)
            ->where('title', 'Contributor track')
            ->firstOrFail();

        $this->asContributor()
            ->url(
                'v1/reciters/%s/albums/%s/tracks/%s',
                $this->reciter->id,
                $this->album->year,
                $track->slug
            )
            ->patch(['title' => 'Contributor track renamed'])
            ->assertOk();

        $track->refresh();

        $this->asContributor()
            ->url(
                'v1/reciters/%s/albums/%s/tracks/%s',
                $this->reciter->id,
                $this->album->year,
                $track->slug
            )
            ->delete()
            ->assertNoContent();

        $this->assertNull(Track::find($track->id));

        $this->asModerator()
            ->url(
                'v1/reciters/%s/albums/%s/tracks',
                $this->reciter->id,
                $this->album->year
            )
            ->post(['title' => 'Moderator track'])
            ->assertOk();

        $trackMod = Track::query()
            ->where('album_id', $this->album->id)
            ->where('title', 'Moderator track')
            ->firstOrFail();

        $this->asModerator()
            ->url(
                'v1/reciters/%s/albums/%s/tracks/%s',
                $this->reciter->id,
                $this->album->year,
                $trackMod->slug
            )
            ->patch(['title' => 'Moderator track renamed'])
            ->assertOk();

        $this->asModerator()
            ->url(
                'v1/reciters/%s/albums/%s/tracks/%s',
                $this->reciter->id,
                $this->album->year,
                $trackMod->fresh()->slug
            )
            ->delete()
            ->assertNoContent();
    }

    /**
     * @test
     */
    public function album_routes_return_404_for_unknown_reciter(): void
    {
        $missingReciterId = Uuid::uuid4()->toString();

        $this->url('v1/reciters/%s/albums', $missingReciterId)
            ->get()
            ->assertNotFound();

        $this->url('v1/reciters/%s/albums/%s', $missingReciterId, '2000')
            ->get()
            ->assertNotFound();

        $this->asModerator()
            ->url('v1/reciters/%s/albums', $missingReciterId)
            ->post(['title' => 'X', 'year' => '2001'])
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function album_routes_return_404_when_year_not_on_reciter(): void
    {
        $this->url('v1/reciters/%s/albums/%s', $this->reciter->id, '1800')
            ->get()
            ->assertNotFound();

        $this->asContributor()
            ->url('v1/reciters/%s/albums/%s', $this->reciter->id, '1800')
            ->patch(['title' => 'Nope'])
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function album_id_under_wrong_reciter_resolves_to_404(): void
    {
        $otherReciter = $this->getReciterFactory()->create();

        $this->url('v1/reciters/%s/albums/%s', $otherReciter->id, $this->album->id)
            ->get()
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function track_belongs_to_album_under_route(): void
    {
        $otherAlbum = $this->getAlbumFactory()->create($this->reciter, [
            'year' => (string) random_int(8000, 8999),
        ]);
        $trackOnOther = $this->getTrackFactory()->create($otherAlbum);

        $this->url(
            'v1/reciters/%s/albums/%s/tracks/%s',
            $this->reciter->id,
            $this->album->year,
            $trackOnOther->slug
        )
            ->get()
            ->assertNotFound();
    }
}
