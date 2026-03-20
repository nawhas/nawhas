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
        // Create a test reciter
        $reciter = $this->getReciterFactory()->create([
            'name' => 'Test Reciter',
            'slug' => 'test-reciter',
            'description' => 'This is a test reciter description.',
        ]);

        // Create a test album
        $album = $this->getAlbumFactory()->create($reciter, [
            'title' => 'Test Album',
            'year' => '2024',
        ]);

        $this->browse(function (Browser $browser) use ($reciter, $album) {
            $page = new ReciterProfile($reciter->slug);

            $browser->visit($page)
                ->assertSee($reciter->name)
                ->assertSee($reciter->description)
                ->assertSee($album->title)
                ->assertSee($album->year);
        });
    }
}
