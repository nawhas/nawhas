<?php

namespace App\Modules\Library\Policies;

use App\Modules\Authentication\Models\User;
use App\Modules\Library\Exceptions\DraftUnavailableException;
use App\Modules\Library\Models\DraftLyrics;
use Illuminate\Support\Facades\Cache;

class DraftLyricsPolicy
{
    public function create(User $user): bool
    {
        return $user->isModerator() || $user->isContributor();
    }

    public function update(User $user, DraftLyrics $draftLyrics): bool
    {
        $lock = Cache::get("draftLyricsForTrack:{$draftLyrics->track_id}");

        if ($lock && $lock != $user->id) {
            throw DraftUnavailableException::forEntity("Lyrics");
        }
        return true;
    }

    public function unlock(User $user, DraftLyrics $draftLyrics): bool
    {
        if ($user->isModerator()) {
            return true;
        }

        $lock = Cache::get("draftLyricsForTrack:{$draftLyrics->track_id}");

        if ($lock && $lock != $user->id) {
            throw DraftUnavailableException::forEntity("Lyrics");
        }

        return true;
    }

    public function delete(User $user): bool
    {
        return $user->isModerator();
    }

    public function publish(User $user): bool
    {
        return $user->isModerator();
    }
}
