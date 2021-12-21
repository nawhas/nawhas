<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DatabaseMigrations;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_allows_user_to_log_in(): void
    {
        $user = $this->getUserFactory()->moderator([
            'email' => 'moderator@nawhas.com',
            'password' => 'secret123',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(100000)
                    ->resize(1920, 1080)
                    ->waitFor('@user-menu')
                    ->click('@user-menu')
                    ->waitFor('@user-menu__login_button')
                    ->click('@user-menu__login-button')
                    ->assertSee('Welcome back!');
        });
    }
}
