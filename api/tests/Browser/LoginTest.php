<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DatabaseMigrations;
use Tests\DuskTestCase;
use Tests\Factories\ModelFactories;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;
    use ModelFactories;

    /**
     * @test
     */
    public function it_allows_user_to_log_in(): void
    {
        $user = $this->getUserFactory()->moderator([
            'email' => 'moderator@nawhas.com',
            'password' => 'secret123',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')
                    ->waitFor('@user-menu__avatar', seconds: 10)
                    ->click('@user-menu__avatar')
                    ->waitFor('@user-menu__login-button')
                    ->click('@user-menu__login-button')
                    ->waitFor('.auth-dialog')
                    ->assertSee('Welcome back!')
                    ->type('.auth-dialog input[type=email]', 'moderator@nawhas.com')
                    ->type('.auth-dialog input[type=password]', 'secret123')
                    ->press('.auth-dialog button[type=submit]')
                    ->waitUntilVue('authenticated', "true", '@user-menu')
                    ->pause(2000)
                    ->click('@user-menu__avatar')
                    ->waitFor('@user-menu__name')
                    ->assertSeeIn('@user-menu__name', $user->name)
                    ->assertSeeIn('@user-menu__email', $user->email);
        });
    }
}
