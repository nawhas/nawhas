<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

class ReciterCreated extends ReciterEvent
{
    public function __construct(
        public string $id,
        public array $attributes = []
    ) {}
}
