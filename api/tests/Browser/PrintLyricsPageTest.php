<?php

namespace Tests\Browser;

use App\Modules\Lyrics\Documents\Format;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\PrintLyrics as PrintLyricsPage;
use Tests\DuskTestCase;
use Throwable;

class PrintLyricsPageTest extends DuskTestCase
{
    /**
     * Test that the print lyrics page renders correctly.
     *
     * @throws Throwable
     */
    public function test_print_lyrics_page_renders(): void
    {
        // Create a test reciter
        $reciter = $this->getReciterFactory()->create();

        // Create a test album
        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Test Album',
            'year' => '2024',
        ]);

        // Create a test track
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Test Track',
        ]);

        // Create and approve lyrics for the track
        $draftLyrics = $this->getDraftLyricsFactory()->create($track, [
            'document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)
        ]);
        $this->getDraftLyricsFactory()->approve($draftLyrics);
        $track->refresh();

        $this->browse(function (Browser $browser) use ($reciter, $album, $track) {
            $page = new PrintLyricsPage($reciter->slug, $album->year, $track->slug);

            $browser->visit($page)
                ->waitFor('@title', 10)
                ->assertSeeIn('@title', $track->title)
                ->assertSeeIn('@meta', $reciter->name)
                ->assertSee($track->lyrics->getContent());
        });
    }
}
