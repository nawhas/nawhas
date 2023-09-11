<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Library;
use Tests\DuskTestCase;
use Throwable;

class LibraryPageTest extends DuskTestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function testLibraryPageWhenNotLoggedIn(): void
    {
        $this->browse(function (Browser $browser) {
            $libraryPage = new Library();
            $browser->logout()->visit($libraryPage);
            $libraryPage->verifyPageWhenUnauthenticated($browser);
        });
    }
}
