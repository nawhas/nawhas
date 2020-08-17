<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Audit\Events\RevisionableEvent;

class TrackTitleChanged extends RevisionableTrackEvent implements RevisionableEvent
{
    public string $title;

    public function __construct(string $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
