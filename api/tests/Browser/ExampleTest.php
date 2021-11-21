<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\HomePage;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Explore the most advanced library of nawhas anywhere.');
        });
    }

    public function testBasicExampleWithPageClass(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new HomePage())
                ->assertSee('Trending This Month');
        });
    }
}
