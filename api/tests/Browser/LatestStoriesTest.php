<?php

namespace Tests\Browser;

use App\Modules\Stories\Models\Story;
use Illuminate\Support\Facades\Cache;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Home;
use Tests\Browser\Pages\ModeratorStoriesIndex;
use Tests\DuskTestCase;
use Throwable;

class LatestStoriesTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_home_shows_latest_stories_and_opens_story_detail(): void
    {
        Story::create([
            'title' => 'Dusk Fixture Nawha Story',
            'slug' => 'dusk-fixture-nawha-story',
            'body' => 'Fixture body content for browser test.',
            'display_date' => '2024-06-15',
            'published' => true,
        ]);

        Cache::flush();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new Home)
                ->waitForText('Latest Stories', 25)
                ->assertSee('Dusk Fixture Nawha Story')
                ->waitFor('.story', 10)
                ->click('.story')
                ->waitForText('Dusk Fixture Nawha Story', 20)
                ->assertPathIs('/stories/2024-06-15/dusk-fixture-nawha-story');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_moderator_stories_list_and_new_story_form(): void
    {
        Story::create([
            'title' => 'Moderator List Fixture',
            'slug' => 'moderator-list-fixture',
            'published' => false,
        ]);

        $user = $this->getUserFactory()->moderator(['password' => 'secret']);

        $this->browse(function (Browser $browser) use ($user) {
            $this->loginViaUi($browser, $user, 'secret');
            $browser->visit(new ModeratorStoriesIndex)
                ->waitFor('@moderator-stories__heading', 15)
                ->assertSeeIn('@moderator-stories__heading', 'Stories')
                ->assertSee('Moderator List Fixture')
                ->click('@moderator-stories__new')
                ->waitFor('[dusk="moderator-stories-new__title"]', 20)
                ->assertSeeIn('[dusk="moderator-stories-new__title"]', 'New story');
        });
    }
}
