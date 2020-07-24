<?php

declare(strict_types=1);

namespace App\Modules\Library\Events\Reciters;

use Spatie\EventSourcing\ShouldBeStored;

class ReciterDescriptionChanged implements ShouldBeStored
{
    public string $id;
    public ?string $description;

    public function __construct(string $id, ?string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }
}
