<?php

namespace Tests\Browser;

use App\Modules\Lyrics\Documents\Format;
use App\Modules\Authentication\Models\User;
use App\Modules\Lyrics\Models\Track;
use App\Modules\Albums\Models\Album;
use App\Modules\Reciters\Models\Reciter;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ResponsiveViewportCoverageTest extends DuskTestCase
{
    private const VIEWPORTS = [
        'desktop' => [1440, 900],
        'tablet' => [1024, 768],
        'mobile' => [375, 812],
    ];

    /**
     * @throws Throwable
     */
    public function test_home_page_responsive(): void
    {
        $this->assertRouteResponsive('/', 'Trending This Month');
    }

    /** @throws Throwable */
    public function test_library_landing_page_responsive(): void
    {
        $this->assertRouteResponsive('/library', 'Welcome to your library');
    }

    /** @throws Throwable */
    public function test_about_page_responsive(): void
    {
        $this->assertRouteResponsive('/about', 'The Journey');
    }

    /** @throws Throwable */
    public function test_reciters_page_responsive(): void
    {
        $this->assertRouteResponsive('/reciters', 'Reciters');
    }

    /** @throws Throwable */
    public function test_reciter_profile_page_responsive(): void
    {
        $fixtures = $this->createFixtures();
        $this->assertRouteResponsive("/reciters/{$fixtures['reciter']->slug}", $fixtures['reciter']->name);
    }

    /** @throws Throwable */
    public function test_album_page_responsive(): void
    {
        $fixtures = $this->createFixtures();
        $this->assertRouteResponsive(
            "/reciters/{$fixtures['reciter']->slug}/albums/{$fixtures['album']->year}",
            $fixtures['album']->title
        );
    }

    /** @throws Throwable */
    public function test_track_page_responsive(): void
    {
        $fixtures = $this->createFixtures();
        $path = "/reciters/{$fixtures['reciter']->slug}/albums/{$fixtures['album']->year}/tracks/{$fixtures['track']->slug}";
        $this->browse(function (Browser $browser) use ($path, $fixtures) {
            foreach (self::VIEWPORTS as $label => [$width, $height]) {
                $browser->resize($width, $height)
                    ->loginAs($fixtures['contributor'])
                    ->visit($path)
                    ->waitForText($fixtures['track']->title, 10)
                    ->assertPresent('.bar__actions--overflow [dusk="edit-draft-lyrics-button"]')
                    ->assertPresent('[dusk="lyrics-card"]');

                $this->assertNoHorizontalOverflow($browser, $label, $path);
            }
        });
    }

    /** @throws Throwable */
    public function test_print_lyrics_page_responsive(): void
    {
        $fixtures = $this->createFixtures();
        $this->assertRouteResponsive(
            "/print/{$fixtures['reciter']->slug}/{$fixtures['album']->year}/{$fixtures['track']->slug}",
            $fixtures['track']->title
        );
    }

    /** @throws Throwable */
    public function test_library_home_page_responsive_for_contributor(): void
    {
        $fixtures = $this->createFixtures();
        $this->browse(function (Browser $browser) use ($fixtures) {
            foreach (self::VIEWPORTS as $label => [$width, $height]) {
                $browser->resize($width, $height)
                    ->loginAs($fixtures['contributor'])
                    ->visit('/library/home')
                    ->waitForText('Recently Saved Nawhas', 10)
                    ->assertSee('Recently Saved Nawhas');

                $this->assertNoHorizontalOverflow($browser, $label, '/library/home');
            }
        });
    }

    /** @throws Throwable */
    public function test_library_tracks_page_responsive_for_contributor(): void
    {
        $fixtures = $this->createFixtures();
        $this->browse(function (Browser $browser) use ($fixtures) {
            foreach (self::VIEWPORTS as $label => [$width, $height]) {
                $browser->resize($width, $height)
                    ->loginAs($fixtures['contributor'])
                    ->visit('/library/tracks')
                    ->waitForText('Saved Nawhas', 10)
                    ->assertSee('Saved Nawhas')
                    ->assertSee($fixtures['track']->title);

                $this->assertNoHorizontalOverflow($browser, $label, '/library/tracks');
            }
        });
    }

    /** @throws Throwable */
    public function test_draft_lyrics_page_responsive_for_moderator(): void
    {
        $fixtures = $this->createFixtures();
        $this->browse(function (Browser $browser) use ($fixtures) {
            foreach (self::VIEWPORTS as $label => [$width, $height]) {
                $browser->resize($width, $height)
                    ->loginAs($fixtures['moderator'])
                    ->visit('/moderator/drafts/lyrics')
                    ->waitForText('Draft Lyrics', 10)
                    ->assertSee('Draft Lyrics');

                $this->assertNoHorizontalOverflow($browser, $label, '/moderator/drafts/lyrics');
            }
        });
    }

    /** @throws Throwable */
    public function test_revisions_page_responsive_for_moderator(): void
    {
        $fixtures = $this->createFixtures();
        $this->browse(function (Browser $browser) use ($fixtures) {
            foreach (self::VIEWPORTS as $label => [$width, $height]) {
                $browser->resize($width, $height)
                    ->loginAs($fixtures['moderator'])
                    ->visit('/moderator/revisions')
                    ->waitForText('Revision History', 10)
                    ->assertSee('Revision History');

                $this->assertNoHorizontalOverflow($browser, $label, '/moderator/revisions');
            }
        });
    }

    /**
     * @throws Throwable
     */
    private function assertRouteResponsive(string $path, string $expectedText): void
    {
        $this->browse(function (Browser $browser) use ($path, $expectedText) {
            foreach (self::VIEWPORTS as $label => [$width, $height]) {
                $browser->resize($width, $height)
                    ->visit($path)
                    ->waitForText($expectedText, 10)
                    ->assertSee($expectedText);

                $this->assertNoHorizontalOverflow($browser, $label, $path);
            }
        });
    }

    /**
     * @return array{contributor: User, moderator: User, reciter: Reciter, album: Album, track: Track}
     */
    private function createFixtures(): array
    {
        $contributor = $this->getUserFactory()->contributor(['password' => 'secret']);
        $moderator = $this->getUserFactory()->moderator(['password' => 'secret']);

        $reciter = $this->getReciterFactory()->create();
        $album = $this->getAlbumFactory()->create($reciter, [
            'year' => '2024',
        ]);
        $track = $this->getTrackFactory()->create($album, [
            'title' => 'Responsive Coverage Track',
            'audio' => 'responsive-coverage-track.mp3',
        ]);

        $draftLyrics = $this->getDraftLyricsFactory()->create($track, [
            'document' => $this->getDraftLyricsFactory()->generateDocument(Format::PlainText),
        ]);
        $this->getDraftLyricsFactory()->approve($draftLyrics);
        $track->refresh();

        $contributor->savedTracks()->attach($track->id, ['created_at' => now()]);

        return [
            'contributor' => $contributor,
            'moderator' => $moderator,
            'reciter' => $reciter,
            'album' => $album,
            'track' => $track,
        ];
    }

    private function assertNoHorizontalOverflow(Browser $browser, string $viewportLabel, string $path): void
    {
        $hasHorizontalOverflow = (bool) $browser->script(
            'return document.documentElement.scrollWidth > (document.documentElement.clientWidth + 1);'
        )[0];

        $this->assertFalse(
            $hasHorizontalOverflow,
            sprintf('Detected horizontal overflow on %s viewport for %s', $viewportLabel, $path)
        );
    }
}
