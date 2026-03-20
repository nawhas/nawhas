<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class LibraryTracks extends Page
{
    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/library/tracks';
    }

    /**
     * Assert that the browser is on the page.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@element' => '#selector',
        ];
    }
}
