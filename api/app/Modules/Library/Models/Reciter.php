<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Entities\Contracts\TimestampedEntity;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use App\Modules\Library\Events\Reciters\{
    ReciterAvatarChanged,
    ReciterCreated,
    ReciterDescriptionChanged,
    ReciterNameChanged,
    ReciterVisited
};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Spatie\Sluggable\{HasSlug, SlugOptions};
use Throwable;

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
        $id = (string)Uuid::uuid1();

        event(new ReciterCreated($id, [
            'name' => $name,
            'description' => $description,
            'avatar' => $avatar,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function retrieve(string $identifier): self
    {
        /** @var Reciter $reciter */
        $reciter = self::query()
            ->when(Uuid::isValid($identifier), fn (Builder $query) => $query->where('id', $identifier))
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        return $reciter;
    }

    /**
     * @throws ModelNotFoundException
     */
    public static function show(string $id): self
    {
        $reciter = self::retrieve($id);

        event(new ReciterVisited($id));

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

    public function persist(): void
    {
        try {
            self::saveOrFail();
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to persist ' . self::class, 0, $e);
        }
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }
}
