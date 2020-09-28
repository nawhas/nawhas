<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Reactors;

use App\Modules\Authentication\Events\PasswordResetRequested;
use App\Modules\Authentication\Mail\PasswordReset as PasswordResetEmail;
use App\Modules\Authentication\Models\User;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class SendPasswordResetEmail extends Reactor
{
    public function onPasswordResetRequested(PasswordResetRequested $event): void
    {
        /** @var User $user */
        $user = User::findOrFail($event->id);

        Mail::to($user)
            ->send(new PasswordResetEmail($user, $event->token));
    }
}
