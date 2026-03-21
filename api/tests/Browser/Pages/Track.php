<?php

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
use Laravel\Dusk\Browser;

class Track extends Page
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
     * @var string
     */
    protected $trackSlug;

    /**
     * Track constructor.
     *
     * @param string $reciterSlug
     * @param string $albumYear
     * @param string $trackSlug
     */
    public function __construct(string $reciterSlug, string $albumYear, string $trackSlug)
    {
        $this->reciterSlug = $reciterSlug;
        $this->albumYear = $albumYear;
        $this->trackSlug = $trackSlug;
    }

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url(): string
    {
        return "/reciters/{$this->reciterSlug}/albums/{$this->albumYear}/tracks/{$this->trackSlug}";
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

        // Wait for the track title to load
        $browser->waitFor('@trackTitle');

        // Check for track metadata
        $browser->waitFor('@reciterName');
        $browser->waitFor('@albumInfo');

        // Check for lyrics section
        $browser->waitFor('@lyricsCard');
    }

    /**
     * Assert that the track title is displayed.
     *
     * @param Browser $browser
     * @param string $title
     * @return void
     */
    public function assertTrackTitle(Browser $browser, string $title): void
    {
        $browser->waitFor('@trackTitle')
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
        $browser->waitFor('@reciterName')
            ->assertSee($name);
    }

    /**
     * Assert that the album year and title are displayed.
     *
     * @param Browser $browser
     * @param string $year
     * @param string $title
     * @return void
     */
    public function assertAlbumInfo(Browser $browser, string $year, string $title): void
    {
        $browser->waitFor('@albumInfo')
            ->assertSee($year)
            ->assertSee($title);
    }

    /**
     * Assert that the play button is visible.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertPlayButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@playButton')
            ->assertVisible('@playButton')
            ->waitForTextIn('@playButton', 'PLAY');
    }

    /**
     * Assert that the stop button is visible.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertStopButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@stopButton')
            ->assertVisible('@stopButton')
            ->waitForTextIn('@stopButton', 'STOP');
    }

    /**
     * Assert that the add to queue button is visible.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertAddToQueueButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@addToQueueButton')
            ->assertVisible('@addToQueueButton')
            ->waitForTextIn('@addToQueueButton', 'ADD TO QUEUE');
    }

    /**
     * Assert that the added to queue button is visible.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertAddedToQueueButtonVisible(Browser $browser): void
    {
        $browser->waitFor('@addedToQueueButton')
            ->assertVisible('@addedToQueueButton')
            ->waitForTextIn('@addedToQueueButton', 'ADDED TO QUEUE');
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
     * Click the play button.
     *
     * @param Browser $browser
     * @return void
     */
    public function clickPlayButton(Browser $browser): void
    {
        $browser->waitFor('@playButton')
            ->click('@playButton');
    }

    /**
     * Click the stop button.
     *
     * @param Browser $browser
     * @return void
     */
    public function clickStopButton(Browser $browser): void
    {
        $browser->waitFor('@stopButton')
            ->click('@stopButton');
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
     * Assert that the lyrics are displayed.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertLyricsVisible(Browser $browser): void
    {
        $browser->waitFor('@lyricsCard')
            ->assertVisible('@lyricsCard');
    }

    /**
     * Assert that the video player is visible.
     *
     * @param Browser $browser
     * @return void
     */
    public function assertVideoPlayerVisible(Browser $browser): void
    {
        $browser->waitFor('@videoPlayer')
            ->assertVisible('@videoPlayer');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements(): array
    {
        return [
            '@trackTitle' => '[dusk="track-title"]',
            '@reciterName' => '[dusk="reciter-name"]',
            '@albumInfo' => '[dusk="album-info"]',
            '@lyricsCard' => '[dusk="lyrics-card"]',
            '@videoPlayer' => '[dusk="video-player"]',
            '@playButton' => '[dusk="play-button"]',
            '@stopButton' => '[dusk="stop-button"]',
            '@addToQueueButton' => '[dusk="add-to-queue-button"]',
            '@addedToQueueButton' => '[dusk="added-to-queue-button"]',
            '@addedToQueueSnackbar' => '[dusk="added-to-queue-snackbar"]',
        ];
    }
}