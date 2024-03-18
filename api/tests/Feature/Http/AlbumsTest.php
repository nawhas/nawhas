<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;
use Tests\Feature\FeatureTestCase;
use Tests\Feature\Http\Responses\AlbumResponse;
use Tests\Feature\Http\Responses\PaginatedCollectionResponse;
use Tests\WithSearchIndex;

use function App\Support\times;

class AlbumsTest extends FeatureTestCase
{
    use WithSearchIndex;

    private const ROUTE_LIST_ALBUMS = 'v1/reciters/%s/albums';
    private const ROUTE_SHOW_ALBUM = 'v1/reciters/%s/albums/%s';

    private Reciter $reciter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reciter = $this->getReciterFactory()->create();
    }

    /**
     * @test
     */
    public function it_retrieves_empty_list_of_albums_when_no_reciter_has_no_albums(): void
    {
        $response = $this->url(self::ROUTE_LIST_ALBUMS, $this->reciter->id)->get();

        PaginatedCollectionResponse::from($response)
            ->assertSuccessful()
            ->assertPage(1)
            ->assertEmpty();
    }

    /**
     * @test
     */
    public function it_retrieves_list_of_albums_for_given_reciter(): void
    {
        /** @var Album[] $albums */
        $albums = times(2, fn() => $this->getAlbumFactory()->create($this->reciter))->all();
        [$a1, $a2] = $albums;

        // Another album for a different reciter.
        $a3 = $this->getAlbumFactory()->create();

        $response = $this->url(self::ROUTE_LIST_ALBUMS, $this->reciter->id)->get();

        PaginatedCollectionResponse::from($response)
            ->withItemFactory(AlbumResponse::getItemFactory())
            ->assertSuccessful()
            ->assertPage(1)
            ->assertTotal(2)
            ->assertTotalPages(1)
            ->items(fn(AlbumResponse $item) => $item->assertSuccessful())
            ->item($a1->id, fn(AlbumResponse $item) => $item->assertMatches($a1))
            ->item($a2->id, fn(AlbumResponse $item) => $item->assertMatches($a2))
            ->assertDoesNotHaveItem($a3->id);
    }

    /**
     * @test
     */
    public function it_retrieves_album_by_id_or_slug(): void
    {
        $album = $this->getAlbumFactory()->create($this->reciter);

        $response = $this->url(self::ROUTE_SHOW_ALBUM, $this->reciter->id, $album->id)->get();
        AlbumResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($album);

        $response = $this->url(self::ROUTE_SHOW_ALBUM, $this->reciter->slug, $album->year)->get();
        AlbumResponse::from($response)
            ->assertSuccessful()
            ->assertMatches($album);
    }
}
