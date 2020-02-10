<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class EntityNotFoundException extends RuntimeException
{
    public function __construct(string $entity, $id)
    {
        parent::__construct("$entity not found with identifier $id");
    }
}
