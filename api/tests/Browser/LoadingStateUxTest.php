<?php

namespace Tests\Browser;

use App\Modules\Authentication\Models\User;
use App\Modules\Lyrics\Documents\Format;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\DraftLyrics;
use Tests\Browser\Pages\LibraryHome;
use Tests\Browser\Pages\Track as TrackPage;
use Tests\DuskTestCase;
use Throwable;

class LoadingStateUxTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_reciters_index_clears_skeleton_loaders_after_fetch(): void
    {
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Loading State Reciter',
        ]);

        $this->browse(function (Browser $browser) use ($reciter) {
            $browser->visit('/reciters')
                ->waitForText('All Reciters', 20)
                ->waitForText($reciter->name, 20)
                ->waitUsing(20, 100, function () use ($browser) {
                    return $browser->driver->executeScript(
                        'return document.querySelectorAll(".view-wrapper .v-skeleton-loader").length === 0;'
                    );
                });
        });
    }

    /**
     * @throws Throwable
     */
    public function test_track_page_replaces_hero_and_lyrics_placeholders_when_ready(): void
    {
        $reciter = $this->getReciterFactory()->create();
        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Loading State Album',
            'year' => '2024',
        ]);
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Loading State Track',
            'audio' => 'test-audio.mp3',
        ]);
        $draftLyrics = $this->getDraftLyricsFactory()->create($track, [
            'document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText),
        ]);
        $this->getDraftLyricsFactory()->approve($draftLyrics);
        $track->refresh();

        $this->browse(function (Browser $browser) use ($reciter, $album, $track) {
            $browser->visit(new TrackPage($reciter->slug, $album->year, $track->slug))
                ->waitFor('@track-title', 20)
                ->waitUsing(20, 100, function () use ($browser) {
                    return $browser->driver->executeScript(
                        'return document.querySelectorAll(".track-page .hero .v-skeleton-loader").length === 0;'
                    );
                })
                ->assertSeeIn('@track-title', $track->title)
                ->waitUntilMissing('.lyrics__content__loader', 20)
                ->assertSeeIn('[dusk="lyrics-card"]', $track->lyrics->getContent());
        });
    }

    /**
     * @throws Throwable
     */
    public function test_library_home_leaves_fetch_pending_skeleton_after_saved_tracks_resolve(): void
    {
        $this->createUsersIfRequired();

        $this->browse(function (Browser $browser) {
            $contributor = User::findByEmail('contributor@nawhas.test');
            $this->loginViaUi($browser, $contributor, 'secret');

            $browser->visit(new LibraryHome())
                ->waitUsing(25, 100, function () use ($browser) {
                    return $browser->driver->executeScript(
                        <<<'JS'
                        const settled = document.querySelector('.saved-tracks-empty') !== null
                          || document.querySelector('.track-card') !== null;
                        const skeletons = document.querySelectorAll('.v-skeleton-loader').length;
                        return settled && skeletons === 0;
                        JS
                    );
                })
                ->assertSee('Recently Saved Nawhas');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_moderator_draft_lyrics_page_exits_loading_indicator(): void
    {
        $user = $this->getUserFactory()->moderator(['password' => 'secret']);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginViaUi($browser, $user, 'secret');

            $browser->visit(new DraftLyrics())
                ->waitFor('@heading', 20)
                ->waitUntilMissing('.draft-lyrics__loading', 20)
                ->waitUsing(15, 100, function () use ($browser) {
                    return $browser->driver->executeScript(
                        'return document.querySelector(".draft-lyrics__empty") !== null || document.querySelector(".draft-lyrics") !== null;'
                    );
                });
        });
    }
}
