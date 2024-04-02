<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Drafts\Lyrics;

use App\Modules\Core\Events\UserAction;

abstract class DraftLyricsEvent extends UserAction
{
    public string $id;
}
