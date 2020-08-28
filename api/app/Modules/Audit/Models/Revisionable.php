<?php

declare(strict_types=1);

namespace App\Modules\Audit\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Revisionable
{
    public function getKey();
    public function revisions(): MorphMany;
}
