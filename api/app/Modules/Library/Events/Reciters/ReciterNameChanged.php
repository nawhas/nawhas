<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReciterNameChanged extends ShouldBeStored
{
    public string $id;
    public string $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
