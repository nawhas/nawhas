<?php

namespace Tests\Browser;

use App\Modules\Lyrics\Documents\Format;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class ResponsiveViewportCoverageTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_critical_pages_render_across_responsive_viewports(): void
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

        $viewports = [
            'desktop' => [1440, 900],
            'tablet' => [1024, 768],
            'mobile' => [375, 812],
        ];

        $publicPages = [
            '/' => 'Trending This Month',
            '/library' => 'Welcome to your library',
            '/about' => 'The Journey',
            '/reciters' => 'Reciters',
            "/reciters/{$reciter->slug}" => $reciter->name,
            "/reciters/{$reciter->slug}/albums/{$album->year}" => $album->title,
            "/reciters/{$reciter->slug}/albums/{$album->year}/tracks/{$track->slug}" => $track->title,
            "/print/{$reciter->slug}/{$album->year}/{$track->slug}" => $track->title,
        ];

        $this->browse(function (Browser $browser) use ($viewports, $publicPages, $contributor, $moderator, $track) {
            foreach ($viewports as $label => [$width, $height]) {
                $browser->resize($width, $height);

                foreach ($publicPages as $path => $expectedText) {
                    $browser->visit($path)
                        ->waitForText($expectedText, 10)
                        ->assertSee($expectedText);

                    $this->assertNoHorizontalOverflow($browser, $label, $path);
                }

                $browser->logout();
                $this->loginViaUi($browser, $contributor, 'secret');

                $browser->visit('/library/home')
                    ->waitForText('Recently Saved Nawhas', 10)
                    ->assertSee('Recently Saved Nawhas');
                $this->assertNoHorizontalOverflow($browser, $label, '/library/home');

                $browser->visit('/library/tracks')
                    ->waitForText('Saved Nawhas', 10)
                    ->assertSee('Saved Nawhas')
                    ->assertSee($track->title);
                $this->assertNoHorizontalOverflow($browser, $label, '/library/tracks');

                $trackPath = "/reciters/{$track->reciter->slug}/albums/{$track->album->year}/tracks/{$track->slug}";
                $browser->visit($trackPath)
                    ->waitForText($track->title, 10)
                    ->assertPresent('.bar__actions--overflow [dusk="edit-draft-lyrics-button"]')
                    ->assertPresent('[dusk="lyrics-card"]');
                $this->assertNoHorizontalOverflow($browser, $label, $trackPath);

                $browser->logout();
                $this->loginViaUi($browser, $moderator, 'secret');

                $browser->visit('/moderator/drafts/lyrics')
                    ->waitForText('Draft Lyrics', 10)
                    ->assertSee('Draft Lyrics');
                $this->assertNoHorizontalOverflow($browser, $label, '/moderator/drafts/lyrics');

                $browser->visit('/moderator/revisions')
                    ->waitForText('Revision History', 10)
                    ->assertSee('Revision History');
                $this->assertNoHorizontalOverflow($browser, $label, '/moderator/revisions');

                $browser->logout();
            }
        });
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
