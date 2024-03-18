<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Events\Saves;

use App\Modules\Core\Events\UserAction;

class SavedTrackRemoved extends UserAction
{
    public function __construct(
        public string $trackId
    ) {}
}
