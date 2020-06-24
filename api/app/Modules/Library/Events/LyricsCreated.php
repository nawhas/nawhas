<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use App\Enum\ChangeType;

class LyricsCreated extends LyricsModified
{
    public function getChangeType(): ChangeType
    {
        return ChangeType::CREATED();
    }
}
