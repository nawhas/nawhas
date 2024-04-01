<?php

namespace App\Modules\Library\Events\Drafts\Lyrics;

class DraftLyricsDeleted extends DraftLyricsEvent
{
    public function __construct(
        public string $id
    ) {}
}
