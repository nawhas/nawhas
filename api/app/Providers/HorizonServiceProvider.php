<?php

namespace App\Providers;

use App\Modules\Authentication\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewHorizon', function (?User $user = null) {
            if (app()->environment('integration', 'local')) {
                return true;
            }

            if (!$user) {
                return false;
            }

            // TODO - switch this to role based access control.
            return in_array($user->email, [
                'szainmehdi@gmail.com',
                'shia786.aa@gmail.com',
                'shea786@live.co.uk',
            ]);
        });
    }
}
