<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Jobs;

use App\Modules\Authentication\Events\PasswordResetRequested;
use App\Modules\Authentication\Models\User;
use App\Modules\Core\Jobs\Job;
use Illuminate\Support\Str;

class RequestPasswordReset extends Job
{
    public function __construct(
        protected string $email
    ) {}

    public function handle(): void
    {
        /** @var User|null $user */
        $user = User::query()
            ->where('email', $this->email)
            ->first();

        if (!$user) {
            return;
        }

        event(new PasswordResetRequested($user->id, $this->generateToken()));
    }

    private function generateToken(): string
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));
    }
}
