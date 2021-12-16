<?php

declare(strict_types=1);

namespace App\Modules\Library\Projectors;

use App\Modules\Audit\Enum\EntityType;
use App\Modules\Library\Events\Reciters\ReciterViewed;
use App\Modules\Library\Events\Tracks\TrackViewed;
use App\Modules\Library\Models\Visit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class VisitsProjector extends Projector implements ShouldQueue
{
    public function onReciterViewed(ReciterViewed $event): void
    {
        Visit::create([
            'visited_at' => $event->visitedAt,
            'visitable_id' => $event->id,
            'visitable_type' => EntityType::RECITER,
        ]);
    }

    public function onTrackViewed(TrackViewed $event): void
    {
        Visit::create([
            'visited_at' => $event->visitedAt,
            'visitable_id' => $event->id,
            'visitable_type' => EntityType::RECITER,
        ]);
    }

    public function resetState(): void
    {
        Visit::truncate();
    }
}
