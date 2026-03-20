<?php

namespace Tests\Browser;

use App\Modules\Lyrics\Documents\Format;
use Tests\Browser\Pages\Track as TrackPage;
use Tests\DuskTestCase;

class TrackPageTest extends DuskTestCase
{
    /**
     * Test that the track page displays correctly.
     *
     * @return void
     */
    public function test_track_page_displays_correctly(): void
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
            'audio' => 'test-audio.mp3',
        ]);

        // Create and approve lyrics for the track
        $draftLyrics = $this->getDraftLyricsFactory()->create($track, [
            'document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText)
        ]);
        $this->getDraftLyricsFactory()->approve($draftLyrics);
        $track->refresh();

        $this->browse(function ($browser) use ($reciter, $album, $track) {
            $browser->visit(new TrackPage($reciter->slug, $album->year, $track->slug))
                ->assertSee($track->title)
                ->assertSee($reciter->name)
                ->assertSee($album->title)
                ->assertSee($album->year)
                ->assertSee($track->lyrics->getContent());
        });
    }

    /**
     * Test that the play button works correctly.
     *
     * @return void
     */
    public function test_play_button_works(): void
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
            'audio' => 'test-audio.mp3',
        ]);

        $this->browse(function ($browser) use ($reciter, $album, $track) {
            $page = new TrackPage($reciter->slug, $album->year, $track->slug);

            $browser->visit($page)
                ->assertPlayButtonVisible()
                ->clickPlayButton()
                // Add assertions for player state if needed
                ->assertSee('STOP');
        });
    }

    /**
     * Test that the add to queue button works correctly.
     *
     * @return void
     */
    public function test_add_to_queue_button_works(): void
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
            'audio' => 'test-audio.mp3',
        ]);

        $this->browse(function ($browser) use ($reciter, $album, $track) {
            $page = new TrackPage($reciter->slug, $album->year, $track->slug);

            $browser->visit($page)
                ->assertAddToQueueButtonVisible()
                ->clickAddToQueueButton()
                // Add assertions for queue state if needed
                ->assertSee('ADDED TO QUEUE');
        });
    }

    /**
     * Test that the video player is displayed when available.
     *
     * @return void
     */
    public function test_video_player_displays_when_available(): void
    {
        // Create a test reciter
        $reciter = $this->getReciterFactory()->create();

        // Create a test album
        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Test Album',
            'year' => '2024',
        ]);

        // Create a test track with video
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Test Track',
            'audio' => 'test-audio.mp3',
            'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->browse(function ($browser) use ($reciter, $album, $track) {
            $page = new TrackPage($reciter->slug, $album->year, $track->slug);

            $browser->visit($page)
                ->assertVideoPlayerVisible();
        });
    }
}
