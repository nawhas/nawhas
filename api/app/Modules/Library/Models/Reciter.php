<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use Carbon\Carbon;
use DateTimeInterface;
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
 * @property string $id
 */
class Reciter extends Model implements TimestampedEntity
{
    use HasSlug;

    protected $keyType = 'string';
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

    public function getCreatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->created_at);
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return Carbon::make($this->updated_at);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }
}
