<?php

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
use Laravel\Dusk\Browser;

class Album extends Page
{
    /**
     * @var string
     */
    protected $reciterSlug;

    /**
     * @var string
     */
    protected $albumYear;

    /**
     * Album constructor.
     *
     * @param string $reciterSlug
     * @param string $albumYear
     */
    public function __construct(string $reciterSlug, string $albumYear)
    {
        $this->reciterSlug = $reciterSlug;
        $this->albumYear = $albumYear;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url(): string
    {
        return "/reciters/{$this->reciterSlug}/albums/{$this->albumYear}";
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

        // Wait for the album artwork to load
        $browser->waitFor('@albumArtwork');

        // Check for album title
        $browser->waitFor('@albumTitle');

        // Check for album metadata
        $browser->waitFor('@albumMeta');

        // Check for tracks section
        $browser->waitFor('@tracksSection');
        $browser->assertSee('Tracks');

        // Check for track list using the dusk selector
        $browser->waitFor('@trackList');
    }

    /**
     * Assert that the album has the expected number of tracks.
     *
     * @param Browser $browser
     * @param int $expectedCount
     * @return void
     */
    public function assertTrackCount(Browser $browser, int $expectedCount): void
    {
        $browser->waitFor('@trackList')
            ->assertVisible('@trackList');

        // Count the number of track items and assert it matches the expected count
        $actualCount = count($browser->elements('@trackItems'));
        assert($actualCount === $expectedCount, "Expected {$expectedCount} tracks, but found {$actualCount}");
    }

    /**
     * Assert that the album page shows the play button.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertPlayButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@playAlbumButton')
            ->assertVisible('@playAlbumButton')
            ->assertSee('PLAY ALBUM');
    }

    /**
     * Assert that the album page shows the add to queue button.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertAddToQueueButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@addToQueueButton')
            ->assertVisible('@addToQueueButton')
            ->assertSee('ADD TO QUEUE');
    }

    /**
     * Assert that the album page shows the added to queue button.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertAddedToQueueButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@addedToQueueButton')
            ->assertVisible('@addedToQueueButton')
            ->assertSee('ADDED TO QUEUE');
    }

    /**
     * Assert that the added to queue snackbar is visible.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertAddedToQueueSnackbarVisible(Browser $browser): void
    {
        $browser->waitFor('@addedToQueueSnackbar')
            ->assertVisible('@addedToQueueSnackbar');
    }

    /**
     * Click the play album button.
     *
     * @param Browser $browser
     * @return void
     */
    public function clickPlayAlbumButton(Browser $browser): void
    {
        $browser->waitFor('@playAlbumButton')
            ->click('@playAlbumButton');
    }

    /**
     * Click the add to queue button.
     *
     * @param Browser $browser
     * @return void
     */
    public function clickAddToQueueButton(Browser $browser): void
    {
        $browser->waitFor('@addToQueueButton')
            ->click('@addToQueueButton');
    }

    /**
     * Assert that the album title is displayed.
     *
     * @param Browser $browser
     * @param string $title
     * @return void
     */
    public function assertAlbumTitle(Browser $browser, string $title): void
    {
        $browser->waitFor('@albumTitle')
            ->assertSee($title);
    }

    /**
     * Assert that the reciter name is displayed.
     *
     * @param Browser $browser
     * @param string $name
     * @return void
     */
    public function assertReciterName(Browser $browser, string $name): void
    {
        $browser->waitFor('@albumMeta')
            ->assertSee($name);
    }

    /**
     * Assert that the album year is displayed.
     *
     * @param Browser $browser
     * @param string $year
     * @return void
     */
    public function assertAlbumYear(Browser $browser, string $year): void
    {
        $browser->waitFor('@albumMeta')
            ->assertSee($year);
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements(): array
    {
        return [
            '@albumArtwork' => '.hero__artwork',
            '@albumTitle' => '.hero__title',
            '@albumMeta' => '.hero__meta',
            '@tracksSection' => '.section__title',
            '@trackList' => '[dusk="track-list"]',
            '@playAlbumButton' => '[dusk="play-album-button"]',
            '@addToQueueButton' => '[dusk="add-to-queue-button"]',
            '@addedToQueueButton' => '[dusk="added-to-queue-button"]',
            '@addedToQueueSnackbar' => '[dusk="added-to-queue-snackbar"]',
            '@trackItems' => '[dusk="track-list"] .v-list-item',
        ];
    }
}
