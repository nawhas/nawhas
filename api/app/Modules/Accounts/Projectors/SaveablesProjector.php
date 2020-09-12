<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Projectors;

use App\Modules\Accounts\Events\Saves\SavedTrackRemoved;
use App\Modules\Accounts\Events\Saves\TrackSaved;
use App\Modules\Authentication\Models\User;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

class SaveablesProjector extends Projector
{
    public function onTrackSaved(TrackSaved $event, StoredEvent $storedEvent): void
    {
        $userId = $event->getUserId();

        if (!$userId) {
            return;
        }

        $user = User::findOrFail($userId);

        if ($user->hasSavedTrack($event->trackId)) {
            return;
        }

        $user->savedTracks()->attach($event->trackId, [
            'created_at' => $storedEvent->created_at,
        ]);
    }

    public function onSavedTrackRemoved(SavedTrackRemoved $event): void
    {
        $userId = $event->getUserId();

        if (!$userId) {
            return;
        }

        $user = User::findOrFail($userId);

        $user->savedTracks()->detach($event->trackId);
    }
}
