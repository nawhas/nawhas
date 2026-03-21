<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FormValidationUxTest extends DuskTestCase
{
    private const ACTIVE_AUTH_DIALOG = '.v-dialog--active .auth-dialog';

    private function openLoginDialog(Browser $browser): void
    {
        $browser->visit('/')
            ->waitFor('@user-menu__avatar', seconds: 10)
            ->click('@user-menu__avatar')
            ->waitFor('@user-menu__login-button')
            ->click('@user-menu__login-button')
            ->waitFor(self::ACTIVE_AUTH_DIALOG)
            ->assertSee('Welcome back!');
    }

    /**
     * @test
     */
    public function login_form_keeps_submit_disabled_until_email_and_password_are_present(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginDialog($browser);
            $browser->assertPresent(self::ACTIVE_AUTH_DIALOG . ' button[type=submit][disabled]')
                ->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                    $dialog->type('input[type=email]', 'guest@nawhas.test');
                })
                ->assertPresent(self::ACTIVE_AUTH_DIALOG . ' button[type=submit][disabled]')
                ->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                    $dialog->type('input[type=password]', 'secret123');
                })
                ->assertNotPresent(self::ACTIVE_AUTH_DIALOG . ' button[type=submit][disabled]');
        });
    }

    /**
     * @test
     */
    public function login_form_shows_server_errors_for_malformed_email(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginDialog($browser);
            $browser->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                $dialog->type('input[type=email]', 'not-an-email')
                    ->type('input[type=password]', 'password123')
                    ->press('button[type=submit]');
            })
                ->waitForText('The email must be a valid email address.', 10);
        });
    }

    /**
     * @test
     */
    public function register_form_shows_duplicate_email_validation_from_server(): void
    {
        $this->getUserFactory()->contributor([
            'email' => 'taken@nawhas.test',
            'password' => 'secret123',
        ]);

        $this->browse(function (Browser $browser) {
            $this->openLoginDialog($browser);
            $browser->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                $dialog->click('@login-form__sign-up');
            })
                ->waitForText('Create an account on Nawhas.com', 10)
                ->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                    $dialog->type('input[type=text]', 'New Signup User')
                        ->type('input[type=email]', 'taken@nawhas.test')
                        ->type('input[type=password]', 'new-password-123')
                        ->press('button[type=submit]');
                })
                ->waitForText('The email has already been taken.', 10);
        });
    }

    /**
     * @test
     */
    public function password_reset_request_shows_server_validation_for_invalid_email(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginDialog($browser);
            $browser->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                $dialog->click('@login-form__forgot-password');
            })
                ->waitForText("Let's get you back into your account.", 10)
                ->within(self::ACTIVE_AUTH_DIALOG, function (Browser $dialog) {
                    $dialog->type('input[type=email]', 'nope')
                        ->press('button[type=submit]');
                })
                ->waitForText('The email must be a valid email address.', 10);
        });
    }

    /**
     * @test
     */
    public function bug_report_form_shows_server_validation_errors(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('@user-menu__avatar', seconds: 10)
                ->click('@user-menu__avatar')
                ->waitForText('Report an issue', 10)
                ->click('@user-menu__report-issue')
                ->waitFor('.bug-report-form')
                ->press('.bug-report-form button[type=submit]')
                ->waitForText('The summary field is required.', 10)
                ->type('input#bug-report-summary', 'Something is wrong on this page')
                ->type('input#bug-report-email', 'not-valid-email')
                ->press('.bug-report-form button[type=submit]')
                ->waitForText('The email must be a valid email address.', 10);
        });
    }
}
