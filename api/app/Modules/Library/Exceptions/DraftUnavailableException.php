<?php

namespace App\Modules\Library\Exceptions;

use RuntimeException;
use Throwable;

class DraftUnavailableException extends RuntimeException
{
    public static function forEntity(string $type): self
    {
        return new self(
            "Draft {$type} is currently locked by another user."
        );
    }
}
