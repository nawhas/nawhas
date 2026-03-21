<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Home;
use Tests\DuskTestCase;
use Throwable;

class HomePageTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function testHomePageRenders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new Home)
                // Test interaction: search input focuses and shows results container
                ->click('input[placeholder="Search Nawhas.com"]')
                ->type('input[placeholder="Search Nawhas.com"]', 'Nadeem')
                ->waitForText('Showing results for “Nadeem”')
                ->assertSee('Showing results for “Nadeem”');
        });
    }
}
