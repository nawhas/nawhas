<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Audit\Models\HasRevisions;
use App\Modules\Audit\Revisionable\Revisionable;
use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use App\Modules\Library\Events\Tracks\TrackAudioChanged;
use App\Modules\Library\Events\Tracks\TrackCreated;
use App\Modules\Library\Events\Tracks\TrackLyricsChanged;
use App\Modules\Library\Events\Tracks\TrackTitleChanged;
use App\Modules\Library\Events\Tracks\TrackVideoChanged;
use App\Modules\Library\Models\Aliases\TrackAlias;
use App\Modules\Library\Models\Traits\Visitable;
use App\Modules\Library\Models\Visits\TrackStatistic;
use App\Modules\Lyrics\Documents\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;
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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $video
 * @property-read \App\Modules\Library\Models\Album $album
 * @property-read \Illuminate\Database\Eloquent\Collection|TrackAlias[] $aliases
 * @property-read int|null $aliases_count
 * @property-read \App\Modules\Library\Models\Reciter $reciter
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Audit\Models\Revision[] $revisions
 * @property-read int|null $revisions_count
 * @property-read TrackStatistic|null $statistics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Library\Models\Visit[] $visits
 * @property-read int|null $visits_count
 * @method static \Illuminate\Database\Eloquent\Builder|Track newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Track newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Track popularAllTime()
 * @method static \Illuminate\Database\Eloquent\Builder|Track popularDay()
 * @method static \Illuminate\Database\Eloquent\Builder|Track popularLast($days)
 * @method static \Illuminate\Database\Eloquent\Builder|Track popularMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Track popularWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Track popularYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Track query()
 * @method static \Illuminate\Database\Eloquent\Builder|Track withTrashed()
 * @property-read \App\Modules\Library\Models\DraftLyrics|null $draftLyrics
 * @mixin \Eloquent
 */
class Track extends Model implements TimestampedEntity, Revisionable
{
    use HasSlug;
    use HasTimestamps;
    use HasUuid;
    use HasRevisions;
    use UsesDataConnection;
    use Visitable;
    use Searchable;

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

    public function changeVideo(?string $url): void
    {
        if ($url !== $this->video) {
            event(new TrackVideoChanged($this->id, $url));
        }
    }

    public function changeLyrics(?Document $document): void
    {
        event(new TrackLyricsChanged($this->id, $document));
    }

    public function getUrlPath(): string
    {
        return "{$this->album->getUrlPath()}/tracks/{$this->slug}";
    }

    public function reciter(): BelongsTo
    {
        return $this->belongsTo(Reciter::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(TrackStatistic::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(TrackAlias::class);
    }

    public function draftLyrics(): HasOne
    {
        return $this->hasOne(DraftLyrics::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    protected function otherRecordExistsWithSlug(string $slug): bool
    {
        $key = $this->getKey();

        if ($this->getIncrementing()) {
            $key ??= '0';
        }

        return static::where($this->slugOptions->slugField, $slug)
            ->where($this->getKeyName(), '!=', $key)
            ->where('album_id', $this->album_id)
            ->withoutGlobalScopes()
            ->exists();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (($field === null || $field === 'id') && Uuid::isValid($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where($field ?? 'slug', $value)->firstOrFail();
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'reciter' => $this->reciter->name,
            'album' => $this->album->title,
            'year' => $this->album->year,
            'lyrics' => $this->lyrics ? $this->lyrics->render() : null,
            'meta' => [
                'slug' => $this->slug,
                'artwork' => $this->album->getArtworkUrl(),
                'url' => sprintf(
                    '/reciters/%s/albums/%s/tracks/%s',
                    $this->reciter->slug,
                    $this->album->year,
                    $this->slug
                ),
            ],
        ];
    }

    /**
     * Filter by popular in all time
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopePopularAllTime($query)
    {
        return $query->join('track_statistics', 'track_statistics.track_id', '=', 'tracks.id')
            ->orderBy('track_statistics.visits_all_time', 'desc');
    }
}
