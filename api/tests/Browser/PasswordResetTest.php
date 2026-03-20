<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\PasswordReset;
use Tests\DuskTestCase;
use Throwable;

class PasswordResetTest extends DuskTestCase
{
    /**
     * Test that the password reset page renders correctly.
     *
     * @throws Throwable
     */
    public function test_password_reset_page_renders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new PasswordReset())
                ->assertSee('Reset Password')
                ->assertVisible('input[type=email]')
                ->assertVisible('button[type=submit]');
        });
    }
}
