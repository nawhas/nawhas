<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

class ReciterCreated extends ReciterEvent
{
    public array $attributes = [];

    public function __construct(string $id, array $attributes)
    {
        $this->id = $id;
        $this->attributes = $attributes;
    }
}
