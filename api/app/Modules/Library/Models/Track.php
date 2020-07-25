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
use App\Modules\Library\Events\Tracks\TrackAudioChanged;
use App\Modules\Library\Events\Tracks\TrackCreated;
use App\Modules\Library\Events\Tracks\TrackTitleChanged;
use App\Modules\Library\Events\Tracks\TrackViewed;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Ramsey\Uuid\Uuid;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property string $id
 */
class Track extends Model implements TimestampedEntity
{
    use HasSlug;
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;

    protected $guarded = [];

    public static function create(Album $album, string $title): self
    {
        $id = Uuid::uuid1()->toString();

        event(new TrackCreated($id, $album->id, [
            'title' => $title,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function retrieve(string $identifier, ?string $albumId = null): self
    {
        if (Uuid::isValid($identifier)) {
            /** @var self $model */
            $model = self::query()->findOrFail($identifier);
            return $model;
        }

        /** @var self $model */
        $model = self::query()
            ->where('album_id', $albumId)
            ->where('slug', $identifier)
            ->firstOrFail();

        return $model;
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function show(string $id): self
    {
        $track = self::retrieve($id);

        event(new TrackViewed($track->id));

        return $track;
    }

    public function changeTitle(string $title): void
    {
        if ($title !== $this->title) {
            event(new TrackTitleChanged($this->id, $title));
        }
    }

    public function changeAudio(?string $path): void
    {
        event(new TrackAudioChanged($this->id, $path));
    }

    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Reciter::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function lyrics(): BelongsTo
    {
        return $this->belongsTo(Lyrics::class, 'lyric_id');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'track_media');
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }
}
