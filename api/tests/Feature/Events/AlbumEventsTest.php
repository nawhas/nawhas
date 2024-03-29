<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Modules\Library\Models\Album;
use App\Modules\Library\Models\Reciter;

use function App\Support\uuid;

class AlbumEventsTest extends EventsTestCase
{
    private Reciter $reciter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reciter = $this->getReciterFactory()->create();
    }

    /**
     * @test
     */
    #[CoversEvent('album.created')]
    public function it_can_replay_album_created_event(): void
    {
        // With no artwork
        $id = uuid();
        $properties = [
            'id' => $id,
            'reciterId' => $this->reciter->id,
            'attributes' => [
                'title' => static::faker()->name,
                'year' => static::faker()->year,
                'artwork' => null,
            ]
        ];
        $this->event('album.created', $properties);

        $this->replay();

        $album = Album::find($id);
        $this->assertNotNull($album);
        $this->assertEquals($this->reciter->id, $album->reciter_id);

        $attributes = $properties['attributes'];
        $this->assertEquals($attributes['title'], $album->title);
        $this->assertEquals($attributes['year'], $album->year);
        $this->assertEquals($attributes['artwork'], $album->artwork);

        // With Artwork
        $id = uuid();
        $properties = [
            'id' => $id,
            'reciterId' => $this->reciter->id,
            'attributes' => [
                'title' => static::faker()->name,
                'year' => static::faker()->year,
                'artwork' => static::faker()->imageUrl,
            ]
        ];
        $this->event('album.created', $properties);

        $this->replay();

        $album = Album::find($id);
        $this->assertNotNull($album);
        $this->assertEquals($properties['attributes']['artwork'], $album->artwork);
    }

    /**
     * @test
     */
    #[CoversEvent('album.changed.title')]
    public function it_can_replay_album_title_changed_event(): void
    {
        $album = $this->getAlbumFactory()->create($this->reciter);
        $title = static::faker()->name;

        $this->assertNotEquals($album->title, $title);

        $this->event('album.changed.title', [
            'id' => $album->id,
            'title' => $title,
        ]);

        $this->replay();

        $album->refresh();
        $this->assertEquals($title, $album->title);
    }

    /**
     * @test
     */
    #[CoversEvent('album.changed.year')]
    public function it_can_replay_album_year_changed_event(): void
    {
        $album = $this->getAlbumFactory()->create($this->reciter);
        $year = static::faker()->year;

        $this->assertNotEquals($album->year, $year);

        $this->event('album.changed.year', [
            'id' => $album->id,
            'year' => $year,
        ]);

        $this->replay();

        $album->refresh();
        $this->assertEquals($year, $album->year);
    }

    /**
     * @test
     */
    #[CoversEvent('album.changed.artwork')]
    public function it_can_replay_album_artwork_changed_event(): void
    {
        $album = $this->getAlbumFactory()->create($this->reciter);
        $artwork = static::faker()->imageUrl;

        $this->assertNotEquals($album->artwork, $artwork);

        $this->event('album.changed.artwork', [
            'id' => $album->id,
            'artwork' => $artwork,
        ]);

        $this->replay();

        $album->refresh();
        $this->assertEquals($artwork, $album->artwork);
    }

    /**
     * @test
     */
    #[CoversEvent('album.deleted')]
    public function it_can_replay_album_deleted_event(): void
    {
        $album = $this->getAlbumFactory()->create($this->reciter);
        $this->event('album.deleted', [
            'id' => $album->id,
        ]);
        $this->replay();

        $this->assertNull(Album::find($album->id));
    }

    /**
     * @test
     */
    #[CoversEvent('album.viewed')]
    public function it_can_replay_album_viewed_event(): void
    {
        $album = $this->getReciterFactory()->create();

        $this->event('album.viewed', [
            'id' => $album->id,
            'userId' => null,
        ]);

        $this->replay();
    }
}
