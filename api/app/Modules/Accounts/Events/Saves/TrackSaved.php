<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Events\Saves;

use App\Modules\Core\Events\UserAction;

class TrackSaved extends UserAction
{
    public string $trackId;

    public function __construct(string $trackId)
    {
        $this->trackId = $trackId;
    }
}
