<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Projectors;

use App\Modules\Authentication\Events\PasswordResetRequested;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class PasswordResetsProjector extends Projector
{
    private const TABLE = 'password_resets';

    private DatabaseManager $database;

    public function __construct(DatabaseManager $database)
    {
        $this->database = $database;
    }

    public function onPasswordResetRequested(PasswordResetRequested $event, StoredEvent $storedEvent): void
    {
        $this->deleteExistingTokens($event->id);

        $this->writeToken($event->id, $event->token, Carbon::make($storedEvent->created_at));
    }

    private function writeToken(string $id, string $token, DateTimeInterface $createdAt): void
    {
        $this->table()->insert([
            'user_id' => $id,
            'token' => $token,
            'created_at' => $createdAt,
        ]);
    }

    private function deleteExistingTokens(string $id): void
    {
        $this->table()->where('user_id', $id)->delete();
    }

    private function table(): Builder
    {
        return $this->database->connection('data')->table(self::TABLE);
    }

}
