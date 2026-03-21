<?php

namespace Tests\Browser\Pages;

use Facebook\WebDriver\Exception\TimeoutException;
use Laravel\Dusk\Browser;

class ModeratorStoriesIndex extends Page
{
    public function url(): string
    {
        return '/moderator/stories';
    }

    /**
     * @throws TimeoutException
     */
    public function assert(Browser $browser): void
    {
        $browser->assertPathIs($this->url());
        $browser->waitFor('@moderator-stories__heading', 15);
        $browser->assertSeeIn('@moderator-stories__heading', 'Stories');
    }

    public function elements(): array
    {
        return [
            '@moderator-stories__heading' => '[dusk="moderator-stories__heading"]',
            '@moderator-stories__new' => '[dusk="moderator-stories__new"]',
        ];
    }
}
