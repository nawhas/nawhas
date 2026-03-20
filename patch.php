<?php
$content = file_get_contents('api/tests/DuskTestCase.php');
$helper = <<<EOT

    protected function loginViaUi(\Laravel\Dusk\Browser \$browser, \App\Modules\Authentication\Models\User \$user, string \$password = 'secret'): void
    {
        \$browser->visit('/')
            ->waitFor('@user-menu__avatar', seconds: 10)
            ->click('@user-menu__avatar')
            ->waitFor('@user-menu__login-button')
            ->click('@user-menu__login-button')
            ->waitFor('.auth-dialog')
            ->type('.auth-dialog input[type=email]', \$user->email)
            ->type('.auth-dialog input[type=password]', \$password)
            ->press('.auth-dialog button[type=submit]')
            ->waitUntilVue('authenticated', "true", '@user-menu')
            ->pause(1000);
    }
EOT;
$content = preg_replace('/}\s*$/', $helper . "\n}\n", $content);
file_put_contents('api/tests/DuskTestCase.php', $content);
