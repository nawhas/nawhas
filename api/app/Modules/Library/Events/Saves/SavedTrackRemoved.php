<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Saves;

use App\Modules\Core\Events\HasUserContext;
use App\Modules\Library\Events\UserAction;

class SavedTrackRemoved extends UserAction
{
    use HasUserContext;

    public string $id;
    public string $trackId;

    public function __construct(string $id, string $trackId)
    {
        $this->id = $id;
        $this->trackId = $trackId;
    }
}
