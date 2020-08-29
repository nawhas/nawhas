<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Tracks;

use App\Modules\Audit\Enum\ChangeType;
use App\Modules\Library\Events\UserAction;

class TrackAudioChanged extends TrackEvent
{
    public ?string $path;

    public function __construct(string $id, ?string $path)
    {
        $this->id = $id;
        $this->path = $path;
    }

    public function changeType(): ChangeType
    {
        return ChangeType::MODIFIED();
    }
}
