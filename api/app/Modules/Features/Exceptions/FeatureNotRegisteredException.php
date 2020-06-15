<?php

declare(strict_types=1);

namespace App\Modules\Features\Exceptions;

use RuntimeException;

class FeatureNotRegisteredException extends RuntimeException
{
    public function __construct(string $feature)
    {
        parent::__construct(
            sprintf('Feature "%s" has not been registered.', $feature)
        );
    }
}
