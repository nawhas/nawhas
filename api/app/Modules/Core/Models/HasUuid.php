<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

/**
 * @property-read string $id
 */
trait HasUuid
{
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
