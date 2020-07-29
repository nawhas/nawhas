<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Reciters\ReciterViewed;
use App\Modules\Library\Events\Tracks\TrackViewed;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PopularEntitiesProjector extends Projector
{
    public function onReciterViewed(ReciterViewed $event): void
    {
        //
    }

    public function onTrackViewed(TrackViewed $event): void
    {
        //
    }
}
