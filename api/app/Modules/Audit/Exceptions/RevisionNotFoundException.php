<?php

declare(strict_types=1);

namespace App\Modules\Audit\Exceptions;

use App\Modules\Audit\Enum\EntityType;
use RuntimeException;

class RevisionNotFoundException extends RuntimeException
{
    public static function forEntity(EntityType $type, string $id): self
    {
        return new self(
            "No existing revision found for {$type->value} with id {$id}"
        );
    }
}
