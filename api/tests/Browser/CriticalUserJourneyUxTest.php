<?php

namespace Tests\Browser;

use App\Modules\Lyrics\Documents\Format;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Album as AlbumPage;
use Tests\Browser\Pages\DraftLyrics;
use Tests\Browser\Pages\Home;
use Tests\Browser\Pages\LibraryTracks;
use Tests\Browser\Pages\ReciterProfile;
use Tests\Browser\Pages\Track as TrackPage;
use Tests\DuskTestCase;
use Tests\WithSearchIndex;
use Throwable;

/**
 * Multi-step browser journeys across routing and UI boundaries (NAW-44).
 * Each test uses a fresh browser session via {@see DuskTestCase::browse()}.
 */
class CriticalUserJourneyUxTest extends DuskTestCase
{
    use WithSearchIndex;

    /**
     * Home → global search → track page → audio controls.
     *
     * @throws Throwable
     */
    public function test_journey_home_search_to_track_playback(): void
    {
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Journey Search Reciter',
            'slug' => 'journey-search-reciter',
        ]);

        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Journey Search Album',
            'year' => '2024',
        ]);

        $unique = bin2hex(random_bytes(6));
        $track = $this->getTrackFactory()->create($album, [
            'title' => "Journey Search Track {$unique}",
            'audio' => 'journey-search.mp3',
        ]);

        $draftLyrics = $this->getDraftLyricsFactory()->create($track, [
            'document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText),
        ]);
        $this->getDraftLyricsFactory()->approve($draftLyrics);
        $track->refresh();

        $track->load(['reciter', 'album']);
        $track->searchable();

        $trackPath = (new TrackPage($reciter->slug, $album->year, $track->slug))->url();

        $this->browse(function (Browser $browser) use ($track, $trackPath) {
            $browser->visit('/')
                ->on(new Home)
                ->click('input[placeholder="Search Nawhas.com"]')
                ->type('input[placeholder="Search Nawhas.com"]', $track->title)
                ->waitForText('Showing results for “'.$track->title.'”', 20)
                ->waitFor('a[href="' . $trackPath . '"]', 20)
                ->click('a[href="' . $trackPath . '"]')
                ->waitForLocation($trackPath, 20)
                ->assertPathIs($trackPath)
                ->waitFor('@trackTitle', 15)
                ->assertPlayButtonVisible()
                ->clickPlayButton()
                ->assertStopButtonVisible()
                ->clickStopButton()
                ->assertPlayButtonVisible();
        });
    }

    /**
     * Reciter profile → album → play album (and queue affordances stay coherent).
     *
     * @throws Throwable
     */
    public function test_journey_reciter_profile_to_album_playback(): void
    {
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Journey Reciter',
            'slug' => 'journey-reciter-flow',
        ]);

        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Journey Album',
            'year' => '2023',
        ]);

        $this->getTrackFactory()->create($album, [
            'title' => 'Journey Track One',
            'audio' => 'journey-one.mp3',
        ]);

        $albumPage = new AlbumPage($reciter->slug, $album->year);

        $this->browse(function (Browser $browser) use ($reciter, $albumPage) {
            $browser->visit(new ReciterProfile($reciter->slug))
                ->waitFor('@title', 15)
                ->assertSeeIn('@title', $reciter->name)
                ->waitFor('@album-title-link', 15)
                ->click('@album-title-link')
                ->waitForLocation($albumPage->url(), 20)
                ->on($albumPage)
                ->assertPlayButtonVisible()
                ->clickPlayAlbumButton()
                ->assertAddToQueueButtonVisible()
                ->clickAddToQueueButton()
                ->assertAddedToQueueButtonVisible()
                ->assertAddedToQueueSnackbarVisible();
        });
    }

    /**
     * Authenticated contributor: save a track from the track page, confirm in library, then remove it.
     *
     * @throws Throwable
     */
    public function test_journey_library_save_track_and_remove(): void
    {
        $user = $this->getUserFactory()->contributor(['password' => 'secret']);

        $reciter = $this->getReciterFactory()->create();
        $album = $this->getAlbumFactory()->create($reciter);
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Journey Library Track',
            'audio' => 'journey-library.mp3',
        ]);

        $trackPage = new TrackPage($reciter->slug, $album->year, $track->slug);

        $this->browse(function (Browser $browser) use ($user, $track, $trackPage) {
            $this->loginViaUi($browser, $user, 'secret');

            $browser->visit($trackPage)
                ->waitFor('@trackTitle', 15)
                ->assertSeeIn('@trackTitle', $track->title)
                ->waitFor('.bar__actions--overflow button.track-favorite', 10)
                ->click('.bar__actions--overflow button.track-favorite');

            $browser->visit(new LibraryTracks())
                ->waitFor('@heading', 15)
                ->waitForText($track->title, 15);

            $browser->visit($trackPage)
                ->waitFor('@trackTitle', 15)
                ->waitFor('.bar__actions--overflow button.track-favorite', 10)
                ->click('.bar__actions--overflow button.track-favorite');

            $browser->visit(new LibraryTracks())
                ->waitFor('@heading', 15)
                ->waitForText('Keep track of nawhas you love.', 20);
        });
    }

    /**
     * Moderator: land on home, sign in, open draft lyrics (skeleton flow across auth + moderator route).
     *
     * @throws Throwable
     */
    public function test_journey_moderator_home_to_draft_lyrics(): void
    {
        $user = $this->getUserFactory()->moderator(['password' => 'secret']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/');
            $this->loginViaUi($browser, $user, 'secret');

            $browser->visit(new DraftLyrics())
                ->waitFor('@heading', 15)
                ->assertSeeIn('@heading', 'Draft Lyrics');
        });
    }
}
