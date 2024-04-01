<?php

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsChanged;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsCreated;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsDeleted;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsPublished;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use App\Modules\Library\Models\DraftLyrics;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DraftLyricsProjector extends Projector
{
    public function onDraftLyricsCreated(DraftLyricsCreated $event)
    {
        $draftLyrics = new DraftLyrics();
        $draftLyrics->id = $event->id;
        $draftLyrics->track_id = $event->trackId;
        $draftLyrics->format = $event->document->getFormat()->value;
        $draftLyrics->content = $event->document->getContent();
        $draftLyrics->save();
    }

    public function onDraftLyricsChanged(DraftLyricsChanged $event)
    {
        $draftLyrics = DraftLyrics::retrieve($event->id);
        $draftLyrics->format = $event->document->getFormat()->value;
        $draftLyrics->content = $event->document->getContent();
        $draftLyrics->save();
    }

    public function onDraftLyricsPublished(DraftLyricsPublished $event)
    {
        $draftLyrics = DraftLyrics::retrieve($event->id);
        event(new TrackLyricsChanged($draftLyrics->track_id, $event->document));
        event(new DraftLyricsDeleted($draftLyrics->id));
    }

    public function onDraftLyricsDeleted(DraftLyricsDeleted $event)
    {
        $draftLyrics = DraftLyrics::retrieve($event->id);
        $draftLyrics->delete();
    }

    public function resetState(): void
    {
        DraftLyrics::truncate();
    }
}
