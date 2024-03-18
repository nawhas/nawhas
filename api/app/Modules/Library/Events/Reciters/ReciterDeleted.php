<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

class ReciterDeleted extends ReciterEvent
{
    public function __construct(
        public string $id,
    ) {}
}
