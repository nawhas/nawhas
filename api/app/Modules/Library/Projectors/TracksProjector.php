<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Tracks\{TrackAudioChanged,
    TrackCreated,
    TrackDeleted,
    TrackLyricsChanged,
    TrackTitleChanged,
    TrackVideoChanged};
use App\Modules\Library\Models\{Album, Track};
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TracksProjector extends Projector
{
    public function onTrackCreated(TrackCreated $event): void
    {
        $album = Album::retrieve($event->albumId);
        $data = collect($event->attributes);
        $data->put('id', $event->id);
        $data->put('reciter_id', $album->reciter_id);

        $track = new Track($data->all());
        $album->tracks()->save($track);
    }

    public function onTrackTitleChanged(TrackTitleChanged $event): void
    {
        $track = Track::retrieve($event->id);
        $track->title = $event->title;
        $track->saveOrFail();
    }

    public function onTrackLyricsChanged(TrackLyricsChanged $event): void
    {
        $track = Track::retrieve($event->id);
        $track->lyrics = $event->document;
        $track->saveOrFail();
    }

    public function onTrackAudioChanged(TrackAudioChanged $event): void
    {
        $track = Track::retrieve($event->id);
        $track->audio = $event->path;
        $track->saveOrFail();
    }

    public function onTrackVideoChanged(TrackVideoChanged $event): void
    {
        $track = Track::retrieve($event->id);
        $track->video = $event->url;
        $track->saveOrFail();
    }

    public function onTrackDeleted(TrackDeleted $event): void
    {
        $track = Track::retrieve($event->id);
        $track->delete();
    }

    public function resetState(): void
    {
        Track::truncate();
    }
}
