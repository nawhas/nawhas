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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Reciter $reciter
 * @method static \Illuminate\Database\Eloquent\Builder|ReciterAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReciterAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReciterAlias query()
 * @mixin \Eloquent
 */
class ReciterAlias extends Alias
{
    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Reciter::class);
    }
}
