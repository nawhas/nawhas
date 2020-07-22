<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Lyrics\{LyricsChanged, LyricsCreated, LyricsDeleted};
use App\Modules\Library\Models\{Lyrics, Track};
use Spatie\EventSourcing\Projectors\{Projector, ProjectsEvents};

class LyricsProjector implements Projector
{
    use ProjectsEvents;

    public function onLyricsCreated(LyricsCreated $event): void
    {
        $track = Track::retrieve($event->trackId);

        $lyrics = new Lyrics([
            'id' => $event->id,
            'content' => $event->content,
            'format' => $event->format,
            'track_id' => $track->id,
        ]);

        $lyrics->saveOrFail();
        $track->lyric_id = $event->id;
        $track->saveOrFail();
    }

    public function onLyricsChanged(LyricsChanged $event): void
    {
        $lyrics = Lyrics::retrieve($event->id);

        $lyrics->update([
            'content' => $event->content,
            'format' => $event->format,
        ]);
    }

    public function onLyricsDeleted(LyricsDeleted $event): void
    {
        $lyrics = Lyrics::retrieve($event->id);
        $lyrics->delete();
    }
}
