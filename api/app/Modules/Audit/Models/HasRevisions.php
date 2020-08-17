<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRevisions
{
    public function getRevisionableAttributes(): array
    {
        $filtered = [
          'id', 'created_at', 'updated_at',
        ];

        return collect($this->attributesToArray())
            ->reject(fn ($value, $key) => in_array($key, $filtered, true))
            ->all();
    }

    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'entity');
    }
}
