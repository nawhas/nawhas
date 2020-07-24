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
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 */
class Album extends Model implements TimestampedEntity
{
    protected $keyType = 'string';
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->created_at);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->updated_at);
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
