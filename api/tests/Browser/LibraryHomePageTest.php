<?php

namespace Tests\Browser;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LibraryHomePage;
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
            $libraryHomePage = new LibraryHomePage();
            $browser->loginAs(User::findByEmail('contributor@nawhas.test'))
                ->visit($libraryHomePage)
                ->logout();

            $browser->loginAs(User::findByEmail('moderator@nawhas.test'))
                ->visit($libraryHomePage)
                ->logout();
        });
    }
}
