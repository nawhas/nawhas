<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ReciterDescriptionChanged extends ShouldBeStored
{
    public string $id;
    public ?string $description;

    public function __construct(string $id, ?string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }
}
