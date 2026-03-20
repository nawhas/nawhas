<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class PrintLyrics extends Page
{
    protected string $reciterSlug;
    protected string $albumYear;
    protected string $trackSlug;

    public function __construct(string $reciterSlug, string $albumYear, string $trackSlug)
    {
        $this->reciterSlug = $reciterSlug;
        $this->albumYear = $albumYear;
        $this->trackSlug = $trackSlug;
    }

    /**
     * Get the URL for the page.
     */
    public function url(): string
    {
        return '/reciters/' . $this->reciterSlug . '/albums/' . $this->albumYear . '/tracks/' . $this->trackSlug . '/print';
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
