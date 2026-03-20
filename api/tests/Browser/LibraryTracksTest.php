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
        $user = $this->getUserFactory()->contributor();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginViaUi($browser, $user);
            $browser->visit(new LibraryTracks())
                ->assertSee('Saved Nawhas');
        });
    }
}
