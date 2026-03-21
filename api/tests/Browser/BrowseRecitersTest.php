<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Reciters;
use Tests\DuskTestCase;

class BrowseRecitersTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     * @throws \Throwable
     */
    public function testExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browseRecitersPage = new Reciters();

            $browser->logout()->visit($browseRecitersPage);
        });
    }
}
