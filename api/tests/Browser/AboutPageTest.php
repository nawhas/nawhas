<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\About as AboutPage;
use Tests\DuskTestCase;
use Throwable;

class AboutPageTest extends DuskTestCase
{
    /**
     * Test that the about page renders correctly.
     *
     * @throws Throwable
     */
    public function test_about_page_renders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new AboutPage())
                ->waitForText('The Journey', 10)
                ->assertSee('The Journey')
                ->assertSee('Credits')
                ->assertSee('Contribute');
        });
    }
}
