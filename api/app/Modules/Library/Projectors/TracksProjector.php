<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Enum\{MediaProvider, MediaType};
use App\Modules\Library\Events\Tracks\{
    TrackAudioChanged,
    TrackCreated,
    TrackDeleted,
    TrackTitleChanged
};
use App\Modules\Library\Models\{Album, Media, Track};
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TracksProjector extends Projector
{
    public function onTrackCreated(TrackCreated $event): void
    {
        $album = Album::retrieve($event->albumId);
        $data = collect($event->attributes);
        $data->put('id', $event->id);

        $track = new Track($data->all());
        $album->tracks()->save($track);
    }

    public function onTrackTitleChanged(TrackTitleChanged $event): void
    {
        $track = Track::retrieve($event->id);
        $track->title = $event->title;
        $track->saveOrFail();
    }

    public function onTrackDeleted(TrackDeleted $event): void
    {
        $track = Track::retrieve($event->id);
        $track->delete();
    }

    public function onTrackAudioChanged(TrackAudioChanged $event): void
    {
        $track = Track::retrieve($event->id);

        $existing = $track->media()
            ->where('type', MediaType::AUDIO)
            ->where('provider', MediaProvider::FILE)
            ->first();

        if ($existing) {
            $track->media()->detach($existing->id);
        }

        $media = new Media([
           'type' => MediaType::AUDIO,
           'provider' => MediaProvider::FILE,
           'path' => $event->path,
        ]);

        $track->media()->save($media);
    }
}
