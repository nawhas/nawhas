<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Field and dialog locators are defined on the Nuxt components (id / dusk), not invented here.
 *
 * @see nuxt/components/auth/LoginForm.vue
 * @see nuxt/components/auth/RegisterForm.vue
 * @see nuxt/components/auth/RequestPasswordResetForm.vue
 * @see nuxt/components/navigation/UserMenu.vue
 * @see nuxt/components/BugReportForm.vue
 */
class FormValidationUxTest extends DuskTestCase
{
    private function openLoginDialog(Browser $browser): void
    {
        $browser->visit('/')
            ->waitFor('@user-menu__avatar', seconds: 10)
            ->click('@user-menu__avatar')
            ->waitFor('@user-menu__login-button')
            ->click('@user-menu__login-button')
            ->waitFor('@user-menu__login-dialog')
            ->assertSee('Welcome back!');
    }

    /**
     * @test
     */
    public function login_form_keeps_submit_disabled_until_email_and_password_are_present(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginDialog($browser);
            $browser->within('@user-menu__login-dialog', function (Browser $dialog) {
                $dialog->assertDisabled('@login-form__submit')
                    ->type('#login-form-email', 'guest@nawhas.test')
                    ->assertDisabled('@login-form__submit')
                    ->type('#login-form-password', 'secret123')
                    ->assertEnabled('@login-form__submit');
            });
        });
    }

    /**
     * @test
     */
    public function login_form_shows_server_errors_for_malformed_email(): void
    {
        $this->browse(function (Browser $browser) {
            $this->openLoginDialog($browser);
            $browser->within('@user-menu__login-dialog', function (Browser $dialog) {
                $dialog->type('#login-form-email', 'not-an-email')
                    ->type('#login-form-password', 'password123')
                    ->press('@login-form__submit');
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
            $browser->within('@user-menu__login-dialog', function (Browser $dialog) {
                $dialog->click('@login-form__sign-up');
            })
                ->waitFor('@user-menu__register-dialog')
                ->waitForText('Create an account on Nawhas.com', 10)
                ->within('@user-menu__register-dialog', function (Browser $dialog) {
                    $dialog->type('#register-form-name', 'New Signup User')
                        ->type('#register-form-email', 'taken@nawhas.test')
                        ->type('#register-form-password', 'new-password-123')
                        ->press('@register-form__submit');
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
            $browser->within('@user-menu__login-dialog', function (Browser $dialog) {
                $dialog->click('@login-form__forgot-password');
            })
                ->waitFor('@user-menu__password-reset-request-dialog')
                ->waitForText("Let's get you back into your account.", 10)
                ->within('@user-menu__password-reset-request-dialog', function (Browser $dialog) {
                    $dialog->type('#reset-request-form-email', 'nope')
                        ->press('@reset-request-form__submit');
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
                ->waitFor('@bug-report-form')
                ->press('@bug-report-form__submit')
                ->waitForText('The summary field is required.', 10)
                ->type('#bug-report-summary', 'Something is wrong on this page')
                ->type('#bug-report-email', 'not-valid-email')
                ->press('@bug-report-form__submit')
                ->waitForText('The email must be a valid email address.', 10);
        });
    }
}
