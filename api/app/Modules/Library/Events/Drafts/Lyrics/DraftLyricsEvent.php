<?php

namespace App\Modules\Library\Events\Drafts\Lyrics;

use App\Modules\Core\Events\UserAction;

class DraftLyricsEvent extends UserAction
{
    public string $id;
}
