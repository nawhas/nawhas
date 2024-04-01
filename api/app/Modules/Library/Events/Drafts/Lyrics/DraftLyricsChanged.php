<?php

namespace App\Modules\Library\Events\Drafts\Lyrics;

use App\Modules\Lyrics\Documents\Document;

class DraftLyricsChanged extends DraftLyricsEvent
{
    public function __construct(
        public string $id,
        public Document $document
    ) {}
}
