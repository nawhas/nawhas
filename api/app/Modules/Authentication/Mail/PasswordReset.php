<?php

namespace App\Modules\Authentication\Mail;

use App\Modules\Authentication\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private User $user;
    private string $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        $domain = config('app.url');

        return $this->markdown('emails.authentication.reset-password')
            ->with('url', "$domain/auth/password/reset/{$this->token}");
    }
}
