<?php

declare(strict_types=1);

namespace App\Modules\Stories\Events\Stories;

class StoryBodyChanged extends StoryEvent
{
    public function __construct(
        public string $id,
        public ?string $body
    ) {}
}
