<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use App\Modules\Library\Events\UserAction;

class ReciterDeleted extends UserAction
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
