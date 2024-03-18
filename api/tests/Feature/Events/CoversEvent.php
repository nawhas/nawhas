<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class CoversEvent
{
    public function __construct(
        public readonly string $event,
        public readonly int $version = 1,
    ) {}
}
