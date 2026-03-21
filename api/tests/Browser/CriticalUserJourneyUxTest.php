<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Album as AlbumPage;
use Tests\Browser\Pages\DraftLyrics;
use Tests\Browser\Pages\Home;
use Tests\Browser\Pages\LibraryTracks;
use Tests\Browser\Pages\ReciterProfile;
use Tests\Browser\Pages\Track as TrackPage;
use Tests\DuskTestCase;
use Throwable;

/**
 * Multi-step browser journeys across routing and UI boundaries (NAW-44).
 * Each test uses a fresh browser session via {@see DuskTestCase::browse()}.
 */
class CriticalUserJourneyUxTest extends DuskTestCase
{
    /**
     * Home → Browse → reciter → album → track page → audio controls.
     *
     * (Global search hits Meilisearch from the browser; indexing from PHPUnit is not
     * guaranteed to be visible before the UI queries, so this journey uses navigation.)
     *
     * @throws Throwable
     */
    public function test_journey_home_browse_to_track_playback(): void
    {
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Journey Browse Reciter',
            'slug' => 'journey-browse-reciter',
        ]);

        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Journey Browse Album',
            'year' => '2024',
        ]);

        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Journey Browse Track',
            'audio' => 'journey-browse.mp3',
        ]);

        $trackPath = (new TrackPage($reciter->slug, $album->year, $track->slug))->url();

        $this->browse(function (Browser $browser) use ($reciter, $track, $trackPath) {
            $browser->visit('/')
                ->on(new Home)
                ->within('@naLinks', static fn (Browser $nav) => $nav->clickLink('Browse'))
                ->waitForLocation('/reciters', 20)
                ->assertPathIs('/reciters')
                ->waitForText('All Reciters', 20)
                ->waitForText($reciter->name, 20)
                ->clickLink($reciter->name)
                ->waitFor('[dusk="reciter-profile__title"]', 20)
                ->assertSeeIn('[dusk="reciter-profile__title"]', $reciter->name)
                ->waitFor('[dusk="album-title-link"]', 15)
                ->click('[dusk="album-title-link"]')
                ->waitFor('[dusk="track-list"]', 20)
                ->clickLink($track->title)
                ->waitForLocation($trackPath, 20)
                ->assertPathIs($trackPath)
                ->waitFor('[dusk="track-title"]', 30)
                ->waitFor('[dusk="play-button"]', 20)
                ->assertVisible('[dusk="play-button"]')
                ->waitForTextIn('[dusk="play-button"]', 'PLAY')
                ->click('[dusk="play-button"]')
                ->waitFor('[dusk="stop-button"]', 20)
                ->assertVisible('[dusk="stop-button"]')
                ->waitForTextIn('[dusk="stop-button"]', 'STOP')
                ->click('[dusk="stop-button"]')
                ->waitFor('[dusk="play-button"]', 20)
                ->assertVisible('[dusk="play-button"]')
                ->waitForTextIn('[dusk="play-button"]', 'PLAY');
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
                ->waitFor('[dusk="reciter-profile__title"]', 15)
                ->assertSeeIn('[dusk="reciter-profile__title"]', $reciter->name)
                ->waitFor('[dusk="album-title-link"]', 15)
                ->click('[dusk="album-title-link"]')
                ->waitForLocation($albumPage->url(), 20)
                ->assertPathIs($albumPage->url())
                ->waitFor('[dusk="track-list"]', 20)
                ->waitFor('[dusk="add-to-queue-button"]', 25)
                ->assertVisible('[dusk="add-to-queue-button"]')
                ->waitForTextIn('[dusk="add-to-queue-button"]', 'ADD TO QUEUE')
                ->click('[dusk="add-to-queue-button"]')
                ->waitFor('[dusk="added-to-queue-button"]', 25)
                ->assertVisible('[dusk="added-to-queue-button"]')
                ->waitForTextIn('[dusk="added-to-queue-button"]', 'ADDED TO QUEUE')
                ->waitFor('[dusk="added-to-queue-snackbar"]', 15)
                ->assertVisible('[dusk="added-to-queue-snackbar"]')
                ->waitFor('[dusk="play-album-button"]', 20)
                ->assertVisible('[dusk="play-album-button"]')
                ->waitForTextIn('[dusk="play-album-button"]', 'PLAY ALBUM')
                ->click('[dusk="play-album-button"]');
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
