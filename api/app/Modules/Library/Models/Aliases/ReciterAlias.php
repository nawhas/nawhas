<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Aliases;

use App\Modules\Library\Models\Reciter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Modules\Library\Models\Aliases\ReciterAlias
 *
 * @property string $id
 * @property string $reciter_id
 * @property string $alias
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Reciter $reciter
 * @method static \Illuminate\Database\Eloquent\Builder|ReciterAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReciterAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReciterAlias query()
 * @mixin \Eloquent
 */
class ReciterAlias extends Alias
{
    protected $fillable = [
        'reciter_id', 'alias', 'created_at', 'updated_at',
    ];

    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Reciter::class);
    }
}
