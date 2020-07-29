<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = ['visited_at', 'visitable_id', 'visitable_type'];

    protected $dates = ['visited_at'];

    public $timestamps = false;
}
