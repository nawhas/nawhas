<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Library\Events\Tracks\TrackViewed;
use App\Modules\Library\Models\Visits\TrackStatistic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TrackStatisticsProjector extends Projector implements ShouldQueue
{
    public function onTrackViewed(TrackViewed $event): void
    {
        /** @var TrackStatistic $visits */
        $visits = TrackStatistic::firstOrNew([
            'track_id' => $event->id
        ]);

        $visits->visits_all_time = ($visits->visits_all_time ?? 0) + 1;

        $visits->save();
    }

    public function resetState(): void
    {
        TrackStatistic::truncate();
    }
}
