<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Library\Events\Albums\{
    AlbumArtworkChanged,
    AlbumCreated,
    AlbumTitleChanged,
    AlbumViewed,
    AlbumYearChanged
};
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * App\Modules\Library\Models\Album
 *
 * @property string $id
 * @property string $reciter_id
 * @property string $title
 * @property string $year
 * @property string|null $artwork
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Library\Models\Reciter $reciter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Library\Models\Track[] $tracks
 * @property-read int|null $tracks_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Album newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Album newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Album query()
 * @mixin \Eloquent
 */
class Album extends Model implements TimestampedEntity
{
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;

    protected $guarded = [];

    public static function create(Reciter $reciter, string $title, string $year, ?string $artwork = null): self
    {
        $id = Uuid::uuid1()->toString();

        event(new AlbumCreated($id, $reciter->id, [
            'title' => $title,
            'year' => $year,
            'artwork' => $artwork,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function retrieve(string $identifier, ?string $reciterId = null): self
    {
        if (Uuid::isValid($identifier)) {
            /** @var self $model */
            $model = self::query()->findOrFail($identifier);
            return $model;
        }

        /** @var self $model */
        $model = self::query()
            ->where('reciter_id', $reciterId)
            ->where('year', $identifier)
            ->firstOrFail();

        return $model;
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function show(string $id): self
    {
        $album = self::retrieve($id);

        event(new AlbumViewed($album->id));

        return $album;
    }

    public function changeTitle(string $title): void
    {
        if ($title !== $this->title) {
            event(new AlbumTitleChanged($this->id, $title));
        }
    }

    public function changeYear(string $year): void
    {
        if ($year !== $this->year) {
            event(new AlbumYearChanged($this->id, $year));
        }
    }

    public function changeArtwork(string $artwork): void
    {
        if ($this->artwork !== $artwork) {
            event(new AlbumArtworkChanged($this->id, $artwork));
        }
    }

    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Reciter::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }
}
