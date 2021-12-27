<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    public function testHomePageRenders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Explore the most advanced library of nawhas online.')
                    ->assertSee('Trending This Month')
                    ->assertSee('Recently Saved Nawhas')
                    ->assertSee('Top Reciters')
                    ->waitFor('.nav__buttons');

            foreach (['Home', 'Browse', 'Library', 'About'] as $link) {
                $browser->assertSeeIn('.nav__buttons', $link);
            }
        });
    }
}
