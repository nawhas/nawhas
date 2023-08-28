<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use Tests\Feature\FeatureTest;
use Tests\Feature\Http\Responses\AlbumResponse;
use Tests\Feature\Http\Responses\PaginatedCollectionResponse;
use Tests\Feature\Http\Responses\TrackResponse;
use Tests\WithSearchIndex;

use function App\Support\times;

class TracksTest extends FeatureTest
{
    use WithSearchIndex;

    private const ROUTE_LIST_TRACKS = 'v1/reciters/%s/albums/%s/tracks';
    private const ROUTE_SHOW_TRACK = 'v1/reciters/%s/albums/%s/tracks/%s';

    private Reciter $reciter;
    private Album $album;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reciter = $this->getReciterFactory()->create();
        $this->album = $this->getAlbumFactory()->create($this->reciter);
    }

    /**
     * @test
     */
    public function it_retrieves_empty_list_of_tracks_when_no_album_empty(): void
    {
        $response = $this->url(self::ROUTE_LIST_TRACKS, $this->reciter->id, $this->album->id)->get();

        PaginatedCollectionResponse::from($response)
            ->assertSuccessful()
            ->assertPage(1)
            ->assertEmpty();
    }

    /**
     * @test
     */
    public function it_retrieves_list_of_tracks_for_given_album(): void
    {
        /** @var Track[] $tracks */
        $tracks = times(2, fn() => $this->getTrackFactory()->create($this->album))->all();
        [$t1, $t2] = $tracks;

        // Another track for a different album
        $t3 = $this->getTrackFactory()->create();

        $response = $this->url(self::ROUTE_LIST_TRACKS, $this->reciter->id, $this->album->id)->get();

        PaginatedCollectionResponse::from($response)
            ->withItemFactory(TrackResponse::getItemFactory())
            ->assertSuccessful()
            ->assertPage(1)
            ->assertTotal(2)
            ->assertTotalPages(1)
            ->items(fn(TrackResponse $item) => $item->assertSuccessful())
            ->item($t1->id, fn(TrackResponse $item) => $item->assertMatches($t1))
            ->item($t2->id, fn(TrackResponse $item) => $item->assertMatches($t2))
            ->assertDoesNotHaveItem($t3->id);
    }

    /**
     * @test
     */
    public function it_retrieves_track_by_id_or_slug(): void
    {
        $track = $this->getTrackFactory()->create($this->album);

        $response = $this->url(self::ROUTE_SHOW_TRACK, $this->reciter->id, $this->album->id, $track->id)->get();
        TrackResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($track);

        $response = $this->url(self::ROUTE_SHOW_TRACK, $this->reciter->slug, $this->album->year, $track->slug)->get();
        TrackResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($track);
    }
}
