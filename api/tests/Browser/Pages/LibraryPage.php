<?php

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\TimeOutException;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class LibraryPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url(): string
    {
        return '/library';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements(): array
    {
        return [
            '@libraryIcon' => 'div[class="library"]  i',
            '@libraryMainHeading' => 'div[class="library"] h1[class="main-heading"]',
            '@librarySubHeading' => 'div[class="library"] h2[class="sub-heading"]',
            '@libraryButton' => 'div[class="library"] button',
            '@libraryButtonText' => 'div[class="library"] button span',
        ];
    }

    /**
     * @throws TimeOutException
     */
    public function verifyPageWhenUnauthenticated(Browser $browser): void
    {
        $browser->waitFor('@libraryIcon');
        $browser->waitFor('@libraryMainHeading')->assertSeeIn('@libraryMainHeading', 'Welcome to your library');
        $browser->waitFor('@librarySubHeading')->assertSeeIn('@librarySubHeading', 'Add nawhas to your favorites, create playlists, and curate your own collection.');
        $browser->waitFor('@libraryButton')->assertButtonEnabled('@libraryButton');
        $browser->waitFor('@libraryButtonText')->assertSeeIn('@libraryButtonText', 'GET STARTED');
    }
}
