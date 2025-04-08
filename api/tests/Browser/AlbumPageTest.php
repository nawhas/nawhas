<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Album as AlbumPage;
use Tests\DuskTestCase;
use Throwable;
use function App\Support\times;

class AlbumPageTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function testAlbumPageRenders(): void
    {
        // Create a test reciter and album in the database
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Test Reciter',
            'slug' => 'test-reciter',
        ]);

        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Test Album',
            'year' => '2023',
        ]);

        // Create some tracks for the album
        times(3, fn() => $this->getTrackFactory()->create($album, []))->all();

        $this->browse(function (Browser $browser) use ($reciter, $album) {
            $page = new AlbumPage($reciter->slug, $album->year);

            $browser->visit($page)
                ->assertAlbumTitle('Test Album')
                ->assertReciterName('Test Reciter')
                ->assertAlbumYear('2023')
                ->assertSee('Tracks')
                ->assertTrackCount(3);
        });
    }

    /**
     * @throws Throwable
     */
    public function testAlbumPageShowsPlayButton(): void
    {
        // Create a test reciter and album in the database
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Test Reciter',
            'slug' => 'test-reciter',
        ]);

        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Test Album',
            'year' => '2023',
        ]);

        // Create a track with audio for the album
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Test Track',
            'audio' => 'test-audio.mp3',
        ]);

        $this->browse(function (Browser $browser) use ($reciter, $album) {
            $page = new AlbumPage($reciter->slug, $album->year);

            $browser->visit($page)
                ->assertPlayButtonVisible()
                ->assertTrackCount(1);
        });
    }
}
