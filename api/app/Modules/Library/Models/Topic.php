<?php

declare(strict_types=1);

namespace App\Modules\Library\Models;

use App\Modules\Core\Contracts\TimestampedEntity;
use App\Modules\Core\Models\HasTimestamps;
use App\Modules\Core\Models\HasUuid;
use App\Modules\Library\Events\Topics\TopicCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Ramsey\Uuid\Uuid;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * App\Modules\Library\Models\Topic
 *
 * @property string $id
 * @property string $name
 * @property string|null $image
 * @property string|null $description
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Topic query()
 * @mixin \Eloquent
 */
class Topic extends Model implements TimestampedEntity
{
    use HasUuid;
    use HasSlug;
    use HasTimestamps;

    protected $fillable = ['name', 'description'];

    public static function create(string $name, ?string $description = null): self
    {
        $id = Uuid::uuid1()->toString();

        event(new TopicCreated($id, [
            'name' => $name,
            'description' => $description,
        ]));

        return self::retrieve($id);
    }

    /**
     * @throws ModelNotFoundException
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

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (($field === null || $field === 'id') && Uuid::isValid($value)) {
            return $this->where('id', $value)->firstOrFail();
        }

        return $this->where($field ?? 'slug', $value)->firstOrFail();
    }
}
