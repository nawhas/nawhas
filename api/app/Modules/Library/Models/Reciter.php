<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Core\Models\UsesDataConnection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Library\Events\Reciters\{
    ReciterAvatarChanged,
    ReciterCreated,
    ReciterDescriptionChanged,
    ReciterNameChanged,
    ReciterViewed
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Reciter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Reciter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Library\Models\Reciter query()
 * @mixin \Eloquent
 */
class Reciter extends Model implements TimestampedEntity
{
    use HasSlug;
    use HasTimestamps;
    use HasUuid;
    use UsesDataConnection;

    protected $guarded = [];

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

    /**
     * @throws ModelNotFoundException
     */
    public static function show(string $id): self
    {
        $reciter = self::retrieve($id);

        event(new ReciterViewed($reciter->id));

        return $reciter;
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

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }
}
