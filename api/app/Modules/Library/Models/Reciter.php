<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Audit\Models\HasRevisions;
use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use App\Modules\Library\Events\Reciters\{ReciterAvatarChanged,
    ReciterCreated,
    ReciterDescriptionChanged,
    ReciterNameChanged};
use App\Modules\Library\Models\Traits\Visitable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Laravel\Scout\Searchable;
use Ramsey\Uuid\Uuid;
use Spatie\Sluggable\{HasSlug, SlugOptions};

/**
 * App\Modules\Library\Models\Reciter
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Library\Models\Album[] $albums
 * @property-read int|null $albums_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Audit\Models\Revision[] $revisions
 * @property-read int|null $revisions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Library\Models\Visit[] $visits
 * @property-read int|null $visits_count
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter popularAllTime()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter popularDay()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter popularLast($days)
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter popularMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter popularWeek()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter popularYear()
 * @method static \Illuminate\Database\Eloquent\Builder|Reciter query()
 * @mixin \Eloquent
 */
class Reciter extends Model implements TimestampedEntity
{
    use HasSlug;
    use HasTimestamps;
    use HasUuid;
    use HasRevisions;
    use UsesDataConnection;
    use Visitable;
    use Searchable;

    protected $guarded = [];

    public function getAvatarUrl(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public static function create(string $name, ?string $description = null, ?string $avatar = null): self
    {
        $id = Uuid::uuid1()->toString();

        event(new ReciterCreated($id, [
            'name' => $name,
            'description' => $description,
            'avatar' => $avatar,
        ]));

        return self::retrieve($id);
    }

    /**
     * @param string $identifier
     * @throws ModelNotFoundException
     * @return Reciter
     */
    public static function retrieve(string $identifier): self
    {
        if (Uuid::isValid($identifier)) {
            /** @var self $model */
            $model = self::query()->findOrFail($identifier);
            return $model;
        }

        /** @var self $model */
        $model = self::query()
            ->where('slug', $identifier)
            ->firstOrFail();

        return $model;
    }

    public function changeName(string $name): void
    {
        if ($name !== $this->name) {
            event(new ReciterNameChanged($this->id, $name));
        }
    }

    public function changeDescription(?string $description): void
    {
        if ($description !== $this->description) {
            event(new ReciterDescriptionChanged($this->id, $description));
        }
    }

    public function changeAvatar(string $avatar): void
    {
        if ($this->avatar !== $avatar) {
            event(new ReciterAvatarChanged($this->id, $avatar));
        }
    }

    public function getUrlPath(): string
    {
        return "/reciters/{$this->slug}";
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (($field === null || $field === 'id') && Uuid::isValid($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where($field ?? 'slug', $value)->firstOrFail();
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        if ($childType === 'album' && Uuid::isValid($value)) {
            return $this->albums()->find($value);
        }

        return parent::resolveChildRouteBinding($childType, $value, $field);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'meta' => [
                'avatar' => $this->getAvatarUrl(),
                'url' => "/reciters/{$this->slug}",
            ],
        ];
    }

    public function deleteReciter()
    {
        $this->albums->each(fn (Album $album) => $album->deleteAlbum());
        $this->delete();
    }
}
