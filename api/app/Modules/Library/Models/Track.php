<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use App\Modules\Library\Events\Tracks\TrackAudioChanged;
use App\Modules\Library\Events\Tracks\TrackCreated;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use App\Modules\Library\Events\Tracks\TrackTitleChanged;
use App\Modules\Library\Events\Tracks\TrackViewed;
use App\Modules\Library\Models\Traits\Visitable;
use App\Modules\Lyrics\Documents\Document;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * App\Modules\Library\Models\Track
 *
 * @property string $id
 * @property string $reciter_id
 * @property string $album_id
 * @property string $title
 * @property string $slug
 * @property string|null $audio
 * @property \App\Modules\Lyrics\Documents\Document|null|null $lyrics
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Library\Models\Album $album
 * @property-read \App\Modules\Library\Models\Reciter $reciter
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Library\Models\Visit[] $visits
 * @property-read int|null $visits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track popularAllTime()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track popularDay()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track popularLast($days)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track popularMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track popularWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Track popularYear()
 */
class Track extends Model implements TimestampedEntity
{
    use HasSlug;
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;
    use Visitable;

    protected $guarded = [];

    protected $casts = [
        'lyrics' => Casts\Lyrics::class,
    ];

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

    public function changeTitle(string $title): void
    {
        if ($title !== $this->title) {
            event(new TrackTitleChanged($this->id, $title));
        }
    }

    public function changeAudio(?string $path): void
    {
        if ($path !== $this->audio) {
            event(new TrackAudioChanged($this->id, $path));
        }
    }

    public function changeLyrics(?Document $document): void
    {
        event(new TrackLyricsChanged($this->id, $document));
    }

    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Reciter::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->allowDuplicateSlugs();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (($field === null || $field === 'id') && Uuid::isValid($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where($field ?? 'slug', $value)->firstOrFail();
    }
}
