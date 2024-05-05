<?php

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
use Laravel\Dusk\Browser;

class Home extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url(): string
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     * @return void
     * @throws TimeoutException
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
        $browser->assertTitle('Home | Nawhas.com');
        $browser->assertSee('Explore the most advanced library of nawhas online.');
        $browser->waitFor('input[placeholder="Search Nawhas.com"]');
        $browser->assertSee('Trending This Month');
        $browser->assertSee('Recently Saved Nawhas');
        $browser->assertSee('The murder of Hussain has lit a fire in the hearts of the believers which will never extinguish.');
        $browser->assertSee('Top Reciters');
        $browser->assertSee('Top Nawhas');

        $browser->waitFor('@naLinks');

        foreach (['Home', 'Browse', 'Library', 'About'] as $link) {
            $browser->assertSeeIn('@naLinks', $link);
        }
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements(): array
    {
        return [
            '@naLinks' => '.nav__buttons',
        ];
    }
}
