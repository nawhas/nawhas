<?php

namespace App\Modules\Library\Events\Drafts\Lyrics;

use App\Modules\Lyrics\Documents\Document;

class DraftLyricsCreated extends DraftLyricsEvent
{
    public function __construct(
        public string $id,
        public string $trackId,
        public Document $document
    ) {}
}
