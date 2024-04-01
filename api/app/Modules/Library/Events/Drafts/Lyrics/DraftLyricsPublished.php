<?php

namespace App\Modules\Library\Events\Drafts\Lyrics;

use App\Modules\Lyrics\Documents\Document;

class DraftLyricsPublished extends DraftLyricsEvent
{
    public function __construct(
        public string $id,
        public Document $document
    ) {}
}
