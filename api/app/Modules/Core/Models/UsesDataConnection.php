<?php

declare(strict_types=1);

namespace App\Modules\Core\Models;

trait UsesDataConnection
{
    public function getConnectionName()
    {
        return 'data';
    }
}
