<?php

namespace Tests\Browser;

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
        $viewports = [
            'desktop' => [1440, 900],
            'tablet' => [1024, 768],
            'mobile' => [375, 812],
        ];

        $criticalPages = [
            '/' => 'Trending This Month',
            '/library' => 'Welcome to your library',
            '/about' => 'The Journey',
        ];

        $this->browse(function (Browser $browser) use ($viewports, $criticalPages) {
            foreach ($viewports as $label => [$width, $height]) {
                $browser->resize($width, $height);

                foreach ($criticalPages as $path => $expectedText) {
                    $browser->visit($path)
                        ->waitForText($expectedText, 10)
                        ->assertSee($expectedText);

                    $hasHorizontalOverflow = (bool) $browser->script(
                        'return document.documentElement.scrollWidth > (document.documentElement.clientWidth + 1);'
                    )[0];

                    $this->assertFalse(
                        $hasHorizontalOverflow,
                        sprintf('Detected horizontal overflow on %s viewport for %s', $label, $path)
                    );

                }
            }
        });
    }
}
