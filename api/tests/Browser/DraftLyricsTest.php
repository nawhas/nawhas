<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\DraftLyrics;
use Tests\DuskTestCase;
use Throwable;

class DraftLyricsTest extends DuskTestCase
{
    /**
     * Test that the draft lyrics page renders correctly for a moderator.
     *
     * @throws Throwable
     */
    public function test_draft_lyrics_page_renders_for_moderator(): void
    {
        $user = $this->getUserFactory()->moderator();

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginViaUi($browser, $user);
            $browser->visit(new DraftLyrics())
                ->assertSee('Draft Lyrics');
        });
    }
}
