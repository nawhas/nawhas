<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Track as TrackPage;
use Tests\DuskTestCase;
use Throwable;

class ErrorStateUxTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_track_page_shows_unavailable_write_up_state_when_lyrics_are_missing(): void
    {
        $reciter = $this->getReciterFactory()->create();
        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'No Lyrics Album',
            'year' => '2024',
        ]);
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'No Lyrics Track',
            'audio' => 'test-audio.mp3',
        ]);

        $this->browse(function (Browser $browser) use ($reciter, $album, $track) {
            $browser->visit(new TrackPage($reciter->slug, $album->year, $track->slug))
                ->waitForText("We don't have a write-up for this nawha yet.")
                ->assertSee("We don't have a write-up for this nawha yet.");
        });
    }

    /**
     * @throws Throwable
     */
    public function test_failed_track_load_shows_404_error_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/reciters/non-existent-reciter/albums/1900/tracks/non-existent-track')
                ->waitForText("Oops! We couldn't find the page you're looking for.")
                ->assertSee('404');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_generic_client_error_shows_recovery_actions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('#__nuxt')
                ->waitForText('Trending This Month');

            $browser->script(<<<'JS'
                const host = document.querySelector('#__nuxt');
                const root = host && host.__vue__ ? host.__vue__ : null;
                const app = root && root.$nuxt ? root.$nuxt : null;
                const errorHandler = app && typeof app.error === 'function'
                  ? app.error.bind(app)
                  : (root && typeof root.error === 'function' ? root.error.bind(root) : null);

                if (errorHandler) {
                  errorHandler({ statusCode: 500, message: 'Synthetic client error' });
                }
            JS);

            $browser->waitForText("Something went wrong. We've been notified and we're working hard to fix it!")
                ->assertSee('Oops!')
                ->assertSee('REFRESH')
                ->assertSee('GO HOME');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_locked_resource_error_shows_423_message_and_actions(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('#__nuxt')
                ->waitForText('Trending This Month');

            $browser->script(<<<'JS'
                const host = document.querySelector('#__nuxt');
                const root = host && host.__vue__ ? host.__vue__ : null;
                const app = root && root.$nuxt ? root.$nuxt : null;
                const errorHandler = app && typeof app.error === 'function'
                  ? app.error.bind(app)
                  : (root && typeof root.error === 'function' ? root.error.bind(root) : null);

                if (errorHandler) {
                  errorHandler({
                    statusCode: 423,
                    message: 'Someone else is working on this write-up right now! Try again in a few minutes.',
                  });
                }
            JS);

            $browser->waitForText('Someone else is working on this write-up right now! Try again in a few minutes.')
                ->assertSee('423')
                ->assertSee('REFRESH')
                ->assertSee('GO HOME');
        });
    }
}
