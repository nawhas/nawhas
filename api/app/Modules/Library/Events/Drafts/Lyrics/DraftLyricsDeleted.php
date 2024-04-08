<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Drafts\Lyrics;

class DraftLyricsDeleted extends DraftLyricsEvent
{
    public function __construct(
        public string $id
    ) {}
}
