<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReciterDeleted extends ShouldBeStored
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
