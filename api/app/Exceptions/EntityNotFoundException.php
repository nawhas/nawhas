<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    public function __construct(string $entity, $id = null)
    {
        $message = "$entity not found";
        if ($id) {
            $message .= " with identifier $id";
        }
        parent::__construct($message);
    }
}
