<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Reciters\ReciterViewed;
use App\Modules\Library\Events\Tracks\TrackViewed;
use App\Modules\Library\Models\Reciter;
use App\Modules\Library\Models\Track;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PopularEntitiesProjector extends Projector
{
    public function onReciterViewed(ReciterViewed $event): void
    {
        $data = collect($event->data);
        $reciter = Reciter::retrieve($event->reciter_id);
    }

    public function onTrackViewed(TrackViewed $event): void
    {
        $data = collect($event->data);

        $track = Track::retrieve($event->track_id);
    }
}
