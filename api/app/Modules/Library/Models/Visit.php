<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Library\Models\Visit
 *
 * @property int $id
 * @property string $visitable_id
 * @property string $visitable_type
 * @property \Illuminate\Support\Carbon $visited_at
 * @method static \Illuminate\Database\Eloquent\Builder|Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit query()
 * @mixin \Eloquent
 */
class Visit extends Model
{
    protected $fillable = ['visited_at', 'visitable_id', 'visitable_type'];

    protected $dates = ['visited_at'];

    public $timestamps = false;
}
