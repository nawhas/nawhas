<?php

declare(strict_types=1);

namespace App\Modules\Popular\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = ['date', 'visitable_id', 'visitable_type'];
}
