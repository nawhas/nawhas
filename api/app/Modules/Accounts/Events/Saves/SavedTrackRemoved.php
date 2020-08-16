<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Events\Saves;

use App\Modules\Library\Events\UserAction;

class SavedTrackRemoved extends UserAction
{
    public string $trackId;

    public function __construct(string $trackId)
    {
        $this->trackId = $trackId;
    }
}
