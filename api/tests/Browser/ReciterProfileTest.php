<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\ReciterProfile;
use Tests\DuskTestCase;
use Throwable;

class ReciterProfileTest extends DuskTestCase
{
    /**
     * Test that the reciter profile page renders correctly.
     *
     * @throws Throwable
     */
    public function test_reciter_profile_page_renders(): void
    {
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Test Reciter',
            'slug' => 'test-reciter',
            'description' => 'This is a test reciter description.',
        ]);

        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Test Album',
            'year' => '2024',
        ]);

        $this->browse(function (Browser $browser) use ($reciter, $album) {
            $page = new ReciterProfile($reciter->slug);

            $browser->visit($page)
                ->waitFor('@title', 10)
                ->assertSeeIn('@title', $reciter->name)
                ->assertSee('Top Nawhas')
                ->assertSee($album->title)
                ->assertSee($album->year)
                // Test interaction: click album link and verify navigation
                ->click('@album-title-link')
                ->waitForLocation("/reciters/{$reciter->slug}/albums/{$album->year}")
                ->assertPathIs("/reciters/{$reciter->slug}/albums/{$album->year}");
        });
    }
}
