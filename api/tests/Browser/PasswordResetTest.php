<?php

namespace Tests\Browser;

use App\Modules\Authentication\Models\PasswordResetToken;
use Laravel\Dusk\Browser;
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
        $user = $this->getUserFactory()->contributor();
        $token = 'dusk-reset-token';
        PasswordResetToken::query()->create([
            'user_id' => $user->id,
            'token' => $token,
            'created_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($token) {
            $browser->visit('/auth/password/reset/' . $token)
                ->waitForText('Reset Password', 10)
                ->assertVisible('input[type=password]');
        });
    }
}
