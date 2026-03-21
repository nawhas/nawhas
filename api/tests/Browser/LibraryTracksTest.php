<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LibraryTracks;
use Tests\DuskTestCase;
use Throwable;

class LibraryTracksTest extends DuskTestCase
{
    /**
     * Test that the library tracks page renders correctly for an authenticated user.
     *
     * @throws Throwable
     */
    public function test_library_tracks_page_renders_for_authenticated_user(): void
    {
        $user = $this->getUserFactory()->contributor(['password' => 'secret']);
        $reciter = $this->getReciterFactory()->create();
        $album = $this->getAlbumFactory()->create($reciter);
        $track = $this->getTrackFactory()->create($album);

        // Add track to user's saved tracks
        $user->savedTracks()->attach($track->id, ['created_at' => now()]);

        $this->browse(function (Browser $browser) use ($user, $track) {
            $this->loginViaUi($browser, $user, 'secret');
            $browser->visit(new LibraryTracks())
                ->waitFor('@heading', 10)
                ->assertSeeIn('@heading', 'Saved Nawhas')
                // Test interaction: track is visible and clicking it navigates to track page
                ->waitForText($track->title)
                ->clickLink($track->title)
                ->waitForLocation("/reciters/{$track->reciter->slug}/albums/{$track->album->year}/tracks/{$track->slug}")
                ->assertPathIs("/reciters/{$track->reciter->slug}/albums/{$track->album->year}/tracks/{$track->slug}");
        });
    }
}
