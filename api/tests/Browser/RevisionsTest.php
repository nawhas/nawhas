<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Revisions;
use Tests\DuskTestCase;
use Throwable;

class RevisionsTest extends DuskTestCase
{
    /**
     * Test that the revisions page renders correctly for a moderator.
     *
     * @throws Throwable
     */
    public function test_revisions_page_renders_for_moderator(): void
    {
        $user = $this->getUserFactory()->moderator(['password' => 'secret']);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginViaUi($browser, $user, 'secret');
            $browser->visit(new Revisions())
                ->waitFor('@heading', 10)
                ->assertSeeIn('@heading', 'Revision History');
        });
    }
}
