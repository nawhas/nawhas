<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Lyrics;

use App\Modules\Library\Events\UserAction;

class LyricsDeleted extends UserAction
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
