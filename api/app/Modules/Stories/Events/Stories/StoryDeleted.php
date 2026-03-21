<?php

declare(strict_types=1);

namespace App\Modules\Stories\Events\Stories;

class StoryDeleted extends StoryEvent
{
    public function __construct(public string $id) {}
}
