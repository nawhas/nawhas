<?php

declare(strict_types=1);

namespace App\Modules\Library\Events;

use Spatie\EventSourcing\ShouldBeStored;

class ReciterCreated implements ShouldBeStored
{
    public array $attributes = [];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }
}
