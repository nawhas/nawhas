<?php

declare(strict_types=1);

namespace App\Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;

class LibrarySave extends Model
{
    /**
     * Get the owning saveableable model.
     */
    public function saveableable()
    {
        return $this->morphTo();
    }
}
