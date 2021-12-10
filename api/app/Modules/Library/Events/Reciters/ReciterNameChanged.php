<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

class ReciterNameChanged extends ReciterEvent
{
    public function __construct(
        public string $id,
        public string $name
    ) {}
}
