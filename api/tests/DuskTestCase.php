<?php

namespace Tests;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Dusk\TestCase as BaseTestCase;
use Tests\Factories\ModelFactories;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;
    use SetUpCustomTraits;
    use DatabaseMigrations;
    use ModelFactories;

    protected function setUpTraits(): array
    {
        $uses = parent::setUpTraits();

        $this->setUpCustomTraits($uses);

        return $uses;
    }

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare(): void
    {
//        if (! static::runningInSail()) {
//            static::startChromeDriver();
//        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return RemoteWebDriver
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
            'ignore-certificate-errors',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
            ]);
        })->all());

        return RemoteWebDriver::create(
            'http://selenium:4444/wd/hub',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    protected function createUsersIfRequired(): void
    {
        try {
            User::findByEmail('contributor@nawhas.test');
        } catch (ModelNotFoundException) {
            User::create(Role::Contributor, 'Contributor One', 'contributor@nawhas.test', 'secret');
        }

        try {
            User::findByEmail('moderator@nawhas.test');
        } catch (ModelNotFoundException) {
            User::create(Role::Moderator, 'Moderator One', 'moderator@nawhas.test', 'secret');
        }
    }

    protected function loginViaUi(\Laravel\Dusk\Browser $browser, User $user, string $password = 'secret'): void
    {
        $browser->visit('/')
            ->waitFor('@user-menu__avatar', seconds: 10)
            ->click('@user-menu__avatar')
            ->waitFor('@user-menu__login-button')
            ->click('@user-menu__login-button')
            ->waitFor('.auth-dialog')
            ->type('.auth-dialog input[type=email]', $user->email)
            ->type('.auth-dialog input[type=password]', $password)
            ->press('.auth-dialog button[type=submit]')
            ->waitUntilVue('authenticated', "true", '@user-menu')
            ->pause(1000);
    }
}
