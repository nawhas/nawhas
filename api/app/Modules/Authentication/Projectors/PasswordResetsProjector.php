<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Projectors;

use App\Modules\Authentication\Events\PasswordResetRequested;
use App\Modules\Authentication\Models\PasswordResetToken;
use Carbon\Carbon;
use DateTimeInterface;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class PasswordResetsProjector extends Projector
{
    public function onPasswordResetRequested(PasswordResetRequested $event): void
    {
        $this->deleteExistingTokens($event->userId);
        $this->writeToken($event->userId, $event->token, $event->createdAt());
    }

    private function writeToken(string $id, string $token, DateTimeInterface $createdAt): void
    {
        PasswordResetToken::create([
            'user_id' => $id,
            'token' => $token,
            'created_at' => $createdAt,
        ]);
    }

    private function deleteExistingTokens(string $id): void
    {
        PasswordResetToken::query()
            ->where('user_id', $id)
            ->delete();
    }
}
