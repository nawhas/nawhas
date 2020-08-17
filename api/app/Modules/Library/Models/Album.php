<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Audit\Models\HasRevisions;
use App\Modules\Audit\Models\Revisionable;
use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use App\Modules\Library\Events\Albums\{AlbumArtworkChanged, AlbumCreated, AlbumTitleChanged, AlbumYearChanged};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Audit\Models\Revision[] $revisions
 * @property-read int|null $revisions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Library\Models\Track[] $tracks
 * @property-read int|null $tracks_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Album newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Album newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Album query()
 * @mixin \Eloquent
 */
class Album extends Model implements TimestampedEntity, Revisionable
{
    use HasTimestamps;
    use HasUuid;
    use HasRevisions;
    use UsesDataConnection;
    use Searchable;

    protected $guarded = [];

    public function getArtworkUrl(): ?string
    {
        return $this->artwork ? Storage::url($this->artwork) : null;
    }

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

    public function resolveRouteBinding($value, $field = null)
    {
        if (($field === null || $field === 'id') && Uuid::isValid($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        throw new ModelNotFoundException('Invalid UUID.');
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        if ($childType === 'track' && Uuid::isValid($value)) {
            return $this->tracks()->find($value);
        }

        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'reciter' => $this->reciter->name,
            'meta' => [
                'artwork' => $this->getArtworkUrl(),
                'url' => sprintf('/reciters/%s/albums/%s', $this->reciter->slug, $this->year),
            ]
        ];
    }
}
