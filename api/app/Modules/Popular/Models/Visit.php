<?php

declare(strict_types=1);

namespace App\Modules\Popular\Models;

use App\Modules\Popular\Events\ModelVisited;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;

class Visit extends Model
{
    protected $fillable = ['date', 'visitable_id', 'visitable_type'];
}
