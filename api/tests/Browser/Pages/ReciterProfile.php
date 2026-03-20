<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class ReciterProfile extends Page
{
    protected string $reciterSlug;

    public function __construct(string $reciterSlug)
    {
        $this->reciterSlug = $reciterSlug;
    }

    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/reciters/' . $this->reciterSlug;
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
            '@title' => '[dusk="reciter-profile__title"]',
            '@albums-section' => '#albums-section',
        ];
    }
}
