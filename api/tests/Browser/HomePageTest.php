<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Home;
use Tests\DuskTestCase;
use Throwable;

class HomePageTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function testHomePageRenders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new Home);
        });
    }
}
