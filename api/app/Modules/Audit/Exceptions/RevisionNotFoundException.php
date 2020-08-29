<?php

declare(strict_types=1);

namespace App\Modules\Audit\Exceptions;

use RuntimeException;

class RevisionNotFoundException extends RuntimeException
{
    public static function forEntity(string $type, string $id): self
    {
        return new self(
            "No existing revision found for {$type} with id {$id}"
        );
    }
}
