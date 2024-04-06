<?php

namespace App\Modules\Library\Models;

use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsChanged;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsCreated;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsDeleted;
use App\Modules\Library\Events\Drafts\Lyrics\DraftLyricsPublished;
use App\Modules\Lyrics\Documents\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Ramsey\Uuid\Uuid;

/**
 * App\Modules\Library\Models\DraftLyrics
 *
 * @property string $id
 * @property string $track_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DraftLyrics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DraftLyrics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DraftLyrics query()
 * @property-read \App\Modules\Library\Models\Track|null $track
 * @property mixed|null $document
 * @mixin \Eloquent
 */
class DraftLyrics extends Model implements TimestampedEntity
{
    use HasTimestamps;
    use UsesDataConnection;
    use HasUuid;

    protected $guarded = [];

    protected $casts = [
        'document' => Casts\Lyrics::class,
    ];

    public static function create(string $trackId, Document $document): self
    {
        $id = Uuid::uuid1()->toString();

        event(new DraftLyricsCreated($id, $trackId, $document));

        return self::retrieve($id);
    }

    public static function retrieve(string $identifier): self
    {
        /** @var self $model */
        $model = self::query()->findOrFail($identifier);
        return $model;
    }

    public function changeDraftLyrics(Document $document): void
    {
        event(new DraftLyricsChanged($this->id, $document));
    }

    public function publishLyrics(Document $document): void
    {
        event(new DraftLyricsPublished($this->id, $document));
        event(new DraftLyricsDeleted($this->id));
    }

    public function deleteDraftLyrics(): void
    {
        event(new DraftLyricsDeleted($this->id));
    }

    public function track(): HasOne
    {
        return $this->hasOne(Track::class, 'id', 'track_id');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->firstOrFail();
    }
}
