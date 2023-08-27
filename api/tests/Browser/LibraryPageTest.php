<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LibraryPage;
use Tests\DuskTestCase;
use Throwable;

class LibraryPageTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     * @throws Throwable
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $libraryPage = new LibraryPage();
            $browser->logout()->visit($libraryPage);
            $libraryPage->verifyPageWhenUnauthenticated($browser);
        });
    }
}
