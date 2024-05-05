<?php

namespace Tests\Browser;

use App\Modules\Authentication\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LibraryHome;
use Tests\DuskTestCase;
use Throwable;

class LibraryHomePageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testLibraryHomePageWhenLoggedIn(): void
    {
        $this->createUsersIfRequired();
        $this->browse(function (Browser $browser) {
            $contributor = User::findByEmail('contributor@nawhas.test');

            $libraryHomePage = new LibraryHome();
            $browser->loginAs($contributor)
                ->assertAuthenticatedAs($contributor)
                ->visit($libraryHomePage)
                ->logout();
        });
    }
}
