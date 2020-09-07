<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

class ReciterDescriptionChanged extends ReciterEvent
{
    public ?string $description;

    public function __construct(string $id, ?string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }
}
