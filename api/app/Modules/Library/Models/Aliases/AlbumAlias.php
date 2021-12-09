<?php

declare(strict_types=1);

namespace App\Modules\Library\Models\Aliases;

use App\Modules\Library\Models\Album;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Modules\Library\Models\Aliases\AlbumAlias
 *
 * @property string $id
 * @property string $reciter_id
 * @property string $album_id
 * @property string $alias
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Album $album
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumAlias newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumAlias newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AlbumAlias query()
 * @mixin \Eloquent
 */
class AlbumAlias extends Alias
{
    protected $fillable = [
        'reciter_id', 'album_id', 'alias', 'created_at', 'updated_at',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
