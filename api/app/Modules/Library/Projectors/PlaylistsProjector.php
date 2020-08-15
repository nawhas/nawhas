<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Playlists\PlaylistCreated;
use App\Modules\Library\Events\Playlists\PlaylistDeleted;
use App\Modules\Library\Events\Playlists\PlaylistNameChanged;
use App\Modules\Library\Events\Playlists\PlaylistTrackAdded;
use App\Modules\Library\Events\Playlists\PlaylistTrackRemoved;
use App\Modules\Library\Models\Playlist;
use App\Modules\Library\Models\PlaylistTracks;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PlaylistsProjector extends Projector
{
    public function onPlaylistCreated(PlaylistCreated $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $playlist = new Playlist($data->all());

        $playlist->saveOrFail();
    }

    public function onPlaylistNameChanged(PlaylistNameChanged $event): void
    {
        $playlist = Playlist::retrieve($event->id);
        $playlist->name = $event->name;

        $playlist->saveOrFail();
    }

    public function onPlaylistDeleted(PlaylistDeleted $event): void
    {
        $playlist = Playlist::retrieve($event->id);

        $playlist->delete();
    }

    public function onPlaylistTrackAdded(PlaylistTrackAdded $event): void
    {
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $playlistTrack = new PlaylistTracks($data->all());

        $playlistTrack->saveOrFail();
    }

    public function onPlaylistTrackRemoved(PlaylistTrackRemoved $event): void
    {
        $playlistTrack = PlaylistTracks::retrieve($event->id);

        $playlistTrack->delete();
    }
}
