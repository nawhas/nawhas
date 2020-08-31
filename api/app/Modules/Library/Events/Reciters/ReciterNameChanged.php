<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

class ReciterNameChanged extends ReciterEvent
{
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
