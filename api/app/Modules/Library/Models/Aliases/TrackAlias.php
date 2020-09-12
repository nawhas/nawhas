<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Aliases;

use App\Modules\Library\Models\Track;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Modules\Library\Models\Aliases\TrackAlias
 *
 * @property string $id
 * @property string $reciter_id
 * @property string $album_id
 * @property string $track_id
 * @property string $alias
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Track $track
 * @method static \Illuminate\Database\Eloquent\Builder|TrackAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TrackAlias query()
 * @mixin \Eloquent
 */
class TrackAlias extends Alias
{
    protected $fillable = [
        'reciter_id', 'album_id', 'track_id', 'alias', 'created_at', 'updated_at',
    ];

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }
}
