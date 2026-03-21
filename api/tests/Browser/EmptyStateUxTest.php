<?php

namespace Tests\Browser;

use App\Modules\Authentication\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LibraryTracks;
use Tests\Browser\Pages\ReciterProfile;
use Tests\Browser\Pages\Revisions;
use Tests\DuskTestCase;
use Throwable;

class EmptyStateUxTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_library_tracks_page_shows_empty_saved_collection_state(): void
    {
        $this->createUsersIfRequired();

        $this->browse(function (Browser $browser) {
            $contributor = User::findByEmail('contributor@nawhas.test');
            $this->loginViaUi($browser, $contributor, 'secret');

            $browser->visit(new LibraryTracks())
                ->waitFor('@heading', 20)
                ->waitFor('.saved-tracks-empty', 20)
                ->assertSee('Keep track of nawhas you love.');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_reciter_profile_shows_empty_albums_message(): void
    {
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Empty Albums Reciter',
        ]);

        $this->browse(function (Browser $browser) use ($reciter) {
            $browser->visit(new ReciterProfile($reciter->slug))
                ->waitFor('@title', 20)
                ->waitForText("We don't have any albums for {$reciter->name} yet.", 20)
                ->assertSee("We don't have any albums for {$reciter->name} yet.");
        });
    }

    /**
     * @throws Throwable
     */
    public function test_moderator_revisions_page_shows_empty_history_state(): void
    {
        $user = $this->getUserFactory()->moderator(['password' => 'secret']);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginViaUi($browser, $user, 'secret');

            $browser->visit(new Revisions())
                ->waitFor('@heading', 20)
                ->waitUsing(20, 100, function () use ($browser) {
                    return $browser->driver->executeScript(
                        'return document.querySelector(".revisions__empty") !== null;'
                    );
                })
                ->assertSee("There's nothing here.");
        });
    }
}
