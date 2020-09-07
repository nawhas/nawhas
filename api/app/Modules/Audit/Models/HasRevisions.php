<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRevisions
{
    public function revisions(): MorphMany
    {
        return $this->morphMany(Revision::class, 'entity');
    }
}
