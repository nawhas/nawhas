<?php

declare(strict_types=1);

namespace App\Support\Resources;

use Illuminate\Http\Resources\MergeValue;

/**
 * @property-read string created_at
 * @property-read string updated_at
 */
trait ResourceHelpers
{
    protected function timestamps(): MergeValue
    {
        return new MergeValue([
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ]);
    }
}
